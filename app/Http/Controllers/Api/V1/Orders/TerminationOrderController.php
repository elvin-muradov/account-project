<?php

namespace App\Http\Controllers\Api\V1\Orders;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Orders\TerminationOrder\TerminationOrderStoreRequest;
use App\Http\Requests\Api\V1\Orders\TerminationOrder\TerminationOrderUpdateRequest;
use App\Http\Resources\Api\V1\Orders\TerminationOrders\TerminationOrderCollection;
use App\Http\Resources\Api\V1\Orders\TerminationOrders\TerminationOrderResource;
use App\Models\Company\Company;
use App\Models\Orders\TerminationOrder;
use App\Traits\HttpResponses;
use Aws\Laravel\AwsFacade as AWS;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use PhpOffice\PhpWord\Exception\CopyFileException;
use PhpOffice\PhpWord\Exception\CreateTemporaryFileException;
use PhpOffice\PhpWord\TemplateProcessor;

class TerminationOrderController extends Controller
{
    use HttpResponses;

    public function index(Request $request): JsonResponse
    {
        $terminationOrders = TerminationOrder::query()
            ->with('company')
            ->paginate($request->input('limit') ?? 10);

        return $this->success(data: new TerminationOrderCollection($terminationOrders));
    }

    /**
     * @throws CopyFileException
     * @throws CreateTemporaryFileException
     */
    public function store(TerminationOrderStoreRequest $request): JsonResponse
    {
        $data = $request->validated();
        $company = $this->getCompany($request->input('company_id'));
        $companyName = $company->company_name;

        $terminationDate = Carbon::parse($request->input('termination_date'))->format('d.m.Y');
        $employmentStartDate = Carbon::parse($request->input('employment_start_date'))->format('d.m.Y');

        $orderNumber = generateOrderNumber(TerminationOrder::class, $company->company_short_name);

        $char1 = substr($terminationDate, '-2');
        $char2 = substr($employmentStartDate, '-2');
        $lastChar1 = getNumberEnd($char1);
        $lastChar2 = getNumberEnd($char2);
        $gender = getGender($request->input('gender'));

        $data = array_merge($data, [
            'order_number' => $orderNumber,
            'last_char1' => $lastChar1,
            'last_char2' => $lastChar2,
            'company_name' => $companyName,
            'gender' => $gender,
            'termination_date' => $terminationDate,
            'employment_start_date' => $employmentStartDate,
            'tax_id_number' => $company->tax_id_number
        ]);

        $documentPath = public_path('assets/order_templates/LEAVING_WORK.docx');
        $fileName = 'TERMINATION_ORDER_' . Str::slug($companyName . $orderNumber, '_') . '.docx';
        $filePath = public_path('assets/termination_orders/' . $fileName);

        $templateProcessor = new TemplateProcessor($documentPath);
        $this->templateProcessor($templateProcessor, $filePath, $data);

        $terminationOrder = TerminationOrder::query()->create([
            'order_number' => $orderNumber,
            'company_id' => $request->input('company_id'),
            'company_name' => $companyName,
            'tax_id_number' => $company->tax_id_number,
            'name' => $request->input('name'),
            'surname' => $request->input('surname'),
            'father_name' => $request->input('father_name'),
            'days_count' => $request->input('days_count'),
            'gender' => $request->input('gender'),
            'termination_date' => $request->input('termination_date'),
            'employment_start_date' => $request->input('employment_start_date'),
            'd_name' => $request->input('d_name'),
            'd_surname' => $request->input('d_surname'),
            'd_father_name' => $request->input('d_father_name'),
            'main_part_of_order' => $request->input('main_part_of_order')
        ]);

        $generatedFilePath = returnOrderFile($filePath, $fileName, 'termination_orders');

        $terminationOrder->update([
            'generated_file' => $generatedFilePath
        ]);

        unlink($filePath);

        return $this->success(data: $terminationOrder, message: 'Xitam əmri uğurla yaradıldı');
    }

    /**
     * @throws CopyFileException
     * @throws CreateTemporaryFileException
     */
    public function update(TerminationOrderUpdateRequest $request, $terminationOrder): JsonResponse
    {
        $data = $request->validated();
        $terminationOrder = TerminationOrder::query()->find($terminationOrder);

        if (!$terminationOrder) {
            return $this->error(message: 'Xitam sənədi tapılmadı', code: 404);
        }

        $orderNumber = $terminationOrder->order_number;
        $company = $this->getCompany($request->input('company_id'));
        $companyName = $company->company_name;
        $terminationDate = Carbon::parse($request->input('termination_date'))->format('d.m.Y');
        $employmentStartDate = Carbon::parse($request->input('employment_start_date'))->format('d.m.Y');

        $char1 = substr($terminationDate, '-2');
        $char2 = substr($employmentStartDate, '-2');
        $lastChar1 = getNumberEnd($char1);
        $lastChar2 = getNumberEnd($char2);
        $gender = getGender($request->input('gender'));

        $data = array_merge($data, [
            'order_number' => $orderNumber,
            'last_char1' => $lastChar1,
            'last_char2' => $lastChar2,
            'company_name' => $companyName,
            'gender' => $gender,
            'termination_date' => $terminationDate,
            'employment_start_date' => $employmentStartDate,
            'tax_id_number' => $company->tax_id_number
        ]);

        $documentPath = public_path('assets/order_templates/LEAVING_WORK.docx');
        $fileName = 'TERMINATION_ORDER_' . Str::slug($companyName . $orderNumber, '_') . '.docx';
        $filePath = public_path('assets/termination_orders/' . $fileName);
        $templateProcessor = new TemplateProcessor($documentPath);
        $this->templateProcessor($templateProcessor, $filePath, $data);

        $terminationOrderCurrentFile = $terminationOrder->generated_file ?? [];

        $s3 = AWS::createClient('s3');
        $s3->deleteObject(array(
            'Bucket' => $terminationOrderCurrentFile[0]['bucket'],
            'Key' => $terminationOrderCurrentFile[0]['generated_name']
        ));

        $generatedFilePath = returnOrderFile($filePath, $fileName, 'termination_orders');

        $terminationOrder->update([
            'company_id' => $request->input('company_id'),
            'company_name' => $companyName,
            'tax_id_number' => $company->tax_id_number,
            'name' => $request->input('name'),
            'surname' => $request->input('surname'),
            'father_name' => $request->input('father_name'),
            'gender' => $request->input('gender'),
            'termination_date' => $request->input('termination_date'),
            'employment_start_date' => $request->input('employment_start_date'),
            'days_count' => $request->input('days_count'),
            'd_name' => $request->input('d_name'),
            'd_surname' => $request->input('d_surname'),
            'd_father_name' => $request->input('d_father_name'),
            'main_part_of_order' => $request->input('main_part_of_order'),
            'generated_file' => $generatedFilePath
        ]);

        unlink($filePath);

        return $this->success(data: $terminationOrder, message: 'Xitam əmri uğurla yeniləndi');
    }

    public function show($terminationOrder): JsonResponse
    {
        $terminationOrder = TerminationOrder::query()->with('company')->find($terminationOrder);

        if (!$terminationOrder) {
            return $this->error(message: 'Xitam əmri tapılmadı', code: 404);
        }

        return $this->success(data: TerminationOrderResource::make($terminationOrder));
    }

    private function getCompany($companyId): Builder|array|Collection|Model
    {
        return Company::query()->with(['mainUser', 'director'])->find($companyId);
    }

    private function templateProcessor(TemplateProcessor $templateProcessor, $filePath, $data): void
    {
        $templateProcessor->setValue('order_number', $data['order_number']);
        $templateProcessor->setValue('company_name', $data['company_name']);
        $templateProcessor->setValue('company_tax_id_number', $data['tax_id_number']);
        $templateProcessor->setValue('name', $data['name']);
        $templateProcessor->setValue('surname', $data['surname']);
        $templateProcessor->setValue('father_name', $data['father_name']);
        $templateProcessor->setValue('gender', $data['gender']);
        $templateProcessor->setValue('employment_start_date', $data['employment_start_date'] . $data['last_char2']);
        $templateProcessor->setValue('termination_date', $data['termination_date'] . $data['last_char1']);
        $templateProcessor->setValue('days_count', $data['days_count']);
        $templateProcessor->setValue('d_name', $data['d_name']);
        $templateProcessor->setValue('d_surname', $data['d_surname']);
        $templateProcessor->setValue('d_father_name', $data['d_father_name']);
        $templateProcessor->setValue('main_part_of_order', $data['main_part_of_order']);
        $templateProcessor->saveAs($filePath);
    }

    public function destroy($terminationOrder): JsonResponse
    {
        $terminationOrder = TerminationOrder::query()->find($terminationOrder);

        if (!$terminationOrder) {
            return $this->error(message: 'Xitam əmri tapılmadı', code: 404);
        }

        $terminationOrderCurrentFile = $terminationOrder->generated_file ?? [];

        $s3 = AWS::createClient('s3');

        $getObject = $s3->listObjects([
            'Bucket' => $terminationOrderCurrentFile[0]['bucket'],
            'Key' => $terminationOrderCurrentFile[0]['generated_name']
        ]);

        if (is_array($getObject['Contents']) && count($getObject['Contents']) > 0) {
            $s3->deleteObject(array(
                'Bucket' => $terminationOrderCurrentFile[0]['bucket'],
                'Key' => $terminationOrderCurrentFile[0]['generated_name']
            ));
        }

        $terminationOrder->delete();

        return $this->success(message: 'Xitam əmri uğurla silindi');
    }
}

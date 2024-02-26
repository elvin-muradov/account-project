<?php

namespace App\Http\Controllers\Api\V1\Orders;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Orders\PregnantOrder\PregnantOrderStoreRequest;
use App\Http\Requests\Api\V1\Orders\PregnantOrder\PregnantOrderUpdateRequest;
use App\Http\Resources\Api\V1\Orders\PregnantHolidayOrders\PregnantHolidayOrderCollection;
use App\Http\Resources\Api\V1\Orders\PregnantHolidayOrders\PregnantHolidayOrderResource;
use App\Models\Company\Company;
use App\Models\Orders\PregnantOrder;
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

class PregnantOrderController extends Controller
{
    use HttpResponses;

    public function index(Request $request): JsonResponse
    {
        $pregnantOrders = PregnantOrder::query()
            ->with('company')
            ->paginate($request->input('limit') ?? 10);

        return $this->success(data: new PregnantHolidayOrderCollection($pregnantOrders));
    }

    /**
     * @throws CopyFileException
     * @throws CreateTemporaryFileException
     */
    public function store(PregnantOrderStoreRequest $request): JsonResponse
    {
        $data = $request->validated();
        $company = $this->getCompany($request->input('company_id'));
        $companyName = $company->company_name;

        $holidayStartDate = Carbon::parse($request->input('holiday_start_date'))->format('d.m.Y');
        $holidayEndDate = Carbon::parse($request->input('holiday_end_date'))->format('d.m.Y');
        $employmentStartDate = Carbon::parse($request->input('employment_start_date'))->format('d.m.Y');

        $gender = getGender($request->input('gender'));

        $data = array_merge($data, [
            'company_name' => $companyName,
            'gender' => $gender,
            'holiday_start_date' => $holidayStartDate,
            'holiday_end_date' => $holidayEndDate,
            'employment_start_date' => $employmentStartDate
        ]);

        $documentPath = public_path('assets/order_templates/PREGNANT_HOLIDAY.docx');
        $fileName = 'PREGNANT_ORDER_' . Str::slug($companyName) . '_' . $request->input('order_number') . '.docx';
        $filePath = public_path('assets/pregnant_orders/' . $fileName);

        $templateProcessor = new TemplateProcessor($documentPath);
        $this->templateProcessor($templateProcessor, $filePath, $data);

        $pregnantOrder = PregnantOrder::query()->create([
            'order_number' => generateOrderNumber(PregnantOrder::class, $company->company_short_name),
            'company_id' => $request->input('company_id'),
            'company_name' => $companyName,
            'tax_id_number' => $request->input('tax_id_number'),
            'name' => $request->input('name'),
            'position' => $request->input('position'),
            'surname' => $request->input('surname'),
            'father_name' => $request->input('father_name'),
            'gender' => $request->input('gender'),
            'type_of_holiday' => $request->input('type_of_holiday'),
            'holiday_start_date' => $request->input('holiday_start_date'),
            'holiday_end_date' => $request->input('holiday_end_date'),
            'employment_start_date' => $request->input('employment_start_date'),
            'd_name' => $request->input('d_name'),
            'd_surname' => $request->input('d_surname'),
            'd_father_name' => $request->input('d_father_name'),
            'main_part_of_order' => $request->input('main_part_of_order')
        ]);

        $generatedFilePath = returnOrderFile($filePath, $fileName, 'pregnant_orders');

        $pregnantOrder->update([
            'generated_file' => $generatedFilePath
        ]);

        unlink($filePath);

        return $this->success(data: $pregnantOrder, message: 'Məzuniyyət əmri uğurla yaradıldı');
    }

    /**
     * @throws CopyFileException
     * @throws CreateTemporaryFileException
     */
    public function update(PregnantOrderUpdateRequest $request, $pregnantOrder): JsonResponse
    {
        $data = $request->validated();
        $pregnantOrder = PregnantOrder::query()->find($pregnantOrder);

        if (!$pregnantOrder) {
            return $this->error(message: 'Məzuniyyət əmri tapılmadı', code: 404);
        }

        $company = $this->getCompany($request->input('company_id'));
        $companyName = $company->company_name;
        $holidayStartDate = Carbon::parse($request->input('holiday_start_date'))->format('d.m.Y');
        $holidayEndDate = Carbon::parse($request->input('holiday_end_date'))->format('d.m.Y');
        $employmentStartDate = Carbon::parse($request->input('employment_start_date'))->format('d.m.Y');

        $gender = getGender($request->input('gender'));

        $data = array_merge($data, [
            'company_name' => $companyName,
            'gender' => $gender,
            'holiday_start_date' => $holidayStartDate,
            'holiday_end_date' => $holidayEndDate,
            'employment_start_date' => $employmentStartDate
        ]);

        $documentPath = public_path('assets/order_templates/PREGNANT_HOLIDAY.docx');
        $fileName = 'PREGNANT_ORDER_' . Str::slug($companyName) . '_' . $request->input('order_number') . '.docx';
        $filePath = public_path('assets/pregnant_orders/' . $fileName);
        $templateProcessor = new TemplateProcessor($documentPath);
        $this->templateProcessor($templateProcessor, $filePath, $data);

        $pregnantOrderCurrentFile = $pregnantOrder->generated_file ?? [];

        $s3 = AWS::createClient('s3');
        $s3->deleteObject(array(
            'Bucket' => $pregnantOrderCurrentFile[0]['bucket'],
            'Key' => $pregnantOrderCurrentFile[0]['generated_name']
        ));

        $generatedFilePath = returnOrderFile($filePath, $fileName, 'pregnant_orders');

        $pregnantOrder->update([
            'company_id' => $request->input('company_id'),
            'company_name' => $companyName,
            'tax_id_number' => $request->input('tax_id_number'),
            'name' => $request->input('name'),
            'surname' => $request->input('surname'),
            'father_name' => $request->input('father_name'),
            'position' => $request->input('position'),
            'gender' => $request->input('gender'),
            'type_of_holiday' => $request->input('type_of_holiday'),
            'holiday_start_date' => $request->input('holiday_start_date'),
            'holiday_end_date' => $request->input('holiday_end_date'),
            'employment_start_date' => $request->input('employment_start_date'),
            'd_name' => $request->input('d_name'),
            'd_surname' => $request->input('d_surname'),
            'd_father_name' => $request->input('d_father_name'),
            'main_part_of_order' => $request->input('main_part_of_order'),
            'generated_file' => $generatedFilePath
        ]);

        unlink($filePath);

        return $this->success(data: $pregnantOrder, message: 'Məzuniyyət əmri uğurla yeniləndi');
    }

    public function show($pregnantOrder): JsonResponse
    {
        $pregnantOrder = PregnantOrder::query()->with('company')->find($pregnantOrder);

        if (!$pregnantOrder) {
            return $this->error(message: 'Məzuniyyət əmri tapılmadı', code: 404);
        }

        return $this->success(data: PregnantHolidayOrderResource::make($pregnantOrder));
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
        $templateProcessor->setValue('position', $data['position']);
        $templateProcessor->setValue('employment_start_date', $data['employment_start_date']);
        $templateProcessor->setValue('holiday_start_date', $data['holiday_start_date']);
        $templateProcessor->setValue('holiday_end_date', $data['holiday_end_date']);
        $templateProcessor->setValue('type_of_holiday', $data['type_of_holiday']);
        $templateProcessor->setValue('d_name', $data['d_name']);
        $templateProcessor->setValue('d_surname', $data['d_surname']);
        $templateProcessor->setValue('d_father_name', $data['d_father_name']);
        $templateProcessor->setValue('main_part_of_order', $data['main_part_of_order']);
        $templateProcessor->saveAs($filePath);
    }

    public function destroy($pregnantOrder): JsonResponse
    {
        $pregnantOrder = PregnantOrder::query()->find($pregnantOrder);

        if (!$pregnantOrder) {
            return $this->error(message: 'Məzuniyyət əmri tapılmadı', code: 404);
        }

        $pregnantOrderCurrentFile = $pregnantOrder->generated_file ?? [];

        $s3 = AWS::createClient('s3');
        $getObject = $s3->listObjects([
            'Bucket' => $pregnantOrderCurrentFile[0]['bucket'],
            'Key' => $pregnantOrderCurrentFile[0]['generated_name']
        ]);

        if (is_array($getObject['Contents']) && count($getObject['Contents']) > 0) {
            $s3->deleteObject(array(
                'Bucket' => $pregnantOrderCurrentFile[0]['bucket'],
                'Key' => $pregnantOrderCurrentFile[0]['generated_name']
            ));
        }

        $pregnantOrder->delete();

        return $this->success(message: 'Məzuniyyət əmri uğurla silindi');
    }
}

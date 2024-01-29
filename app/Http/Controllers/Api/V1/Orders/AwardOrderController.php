<?php

namespace App\Http\Controllers\Api\V1\Orders;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Orders\AwardOrder\AwardOrderStoreRequest;
use App\Http\Requests\Api\V1\Orders\AwardOrder\AwardOrderUpdateRequest;
use App\Models\Company\Company;
use App\Models\Orders\AwardOrder;
use App\Traits\HttpResponses;
use Aws\Laravel\AwsFacade as AWS;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use PhpOffice\PhpWord\Element\Section;
use PhpOffice\PhpWord\Exception\CopyFileException;
use PhpOffice\PhpWord\Exception\CreateTemporaryFileException;
use PhpOffice\PhpWord\Exception\Exception;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\TemplateProcessor;
use PhpOffice\PhpWord\Writer\Word2007;

class AwardOrderController extends Controller
{
    use HttpResponses;

    /**
     * @throws Exception
     * @throws CreateTemporaryFileException
     * @throws CopyFileException
     */
    public function store(AwardOrderStoreRequest $request): JsonResponse
    {
        $data = $request->validated();

        $company = $this->getCompany($request->input('company_id'));
        $companyName = $company->company_name;

        $orderDate = Carbon::parse($request->input('order_date'))->format('d.m.Y');
        $char = substr($orderDate, '-1');
        $lastCharOD = getNumberEnd($char);

        $data = array_merge($data, [
            'company_name' => $companyName,
            'order_date' => $orderDate,
            'last_char_od' => $lastCharOD
        ]);

        $documentPath = public_path('assets/order_templates/AWARD.docx');
        $fileName = 'AWARD_ORDER_' . Str::slug($companyName) . '_'
            . $request->input('order_number') . '.docx';
        $filePath = public_path('assets/award_orders/' . $fileName);

        $templateProcessor = new TemplateProcessor($documentPath);
        $this->templateProcessor($templateProcessor, $filePath, $data);

        $awardOrder = AwardOrder::query()->create([
            'order_number' => $request->input('order_number'),
            'company_id' => $request->input('company_id'),
            'company_name' => $companyName,
            'tax_id_number' => $request->input('tax_id_number'),
            'order_date' => $request->input('order_date'),
            'd_name' => $request->input('d_name'),
            'd_surname' => $request->input('d_surname'),
            'd_father_name' => $request->input('d_father_name'),
            'main_part_of_order' => $request->input('main_part_of_order'),
            'worker_infos' => $request->input('worker_infos')
        ]);

        $generatedFilePath = returnOrderFile($filePath, $fileName, 'award_orders');

        $awardOrder->update([
            'generated_file' => $generatedFilePath
        ]);

        unlink($filePath);

        return $this->success(data: $awardOrder, message: 'Mükafat əmri uğurla yaradıldı');
    }

    /**
     * @throws CopyFileException
     * @throws CreateTemporaryFileException
     */
    public function update(AwardOrderUpdateRequest $request, $awardOrder): JsonResponse
    {
        $data = $request->validated();
        $awardOrder = AwardOrder::query()->find($awardOrder);

        if (!$awardOrder) {
            return $this->error(message: 'Mükafat əmri tapılmadı', code: 404);
        }

        $company = $this->getCompany($request->input('company_id'));
        $companyName = $company->company_name;
        $orderDate = Carbon::parse($request->input('order_date'))->format('d.m.Y');

        $char = substr($orderDate, '-1');

        $lastCharOD = getNumberEnd($char);

        $data = array_merge($data, [
            'last_char_od' => $lastCharOD,
            'company_name' => $companyName,
            'order_date' => $orderDate
        ]);

        $documentPath = public_path('assets/order_templates/AWARD.docx');
        $fileName = 'AWARD_ORDER_' . Str::slug($companyName) .
            '_' . $request->input('order_number') . '.docx';
        $filePath = public_path('assets/award_orders/' . $fileName);
        $templateProcessor = new TemplateProcessor($documentPath);
        $this->templateProcessor($templateProcessor, $filePath, $data);

        $awardOrderCurrentFile = $awardOrder->generated_file ?? [];

        $s3 = AWS::createClient('s3');
        $s3->deleteObject(array(
            'Bucket' => $awardOrderCurrentFile[0]['bucket'],
            'Key' => $awardOrderCurrentFile[0]['generated_name']
        ));

        $generatedFilePath = returnOrderFile($filePath, $fileName, 'award_orders');

        $awardOrder->update([
            'order_number' => $request->input('order_number'),
            'company_id' => $request->input('company_id'),
            'company_name' => $companyName,
            'tax_id_number' => $request->input('tax_id_number'),
            'order_date' => $request->input('order_date'),
            'd_name' => $request->input('d_name'),
            'd_surname' => $request->input('d_surname'),
            'd_father_name' => $request->input('d_father_name'),
            'main_part_of_order' => $request->input('main_part_of_order'),
            'worker_infos' => $request->input('worker_infos'),
            'generated_file' => $generatedFilePath
        ]);

        unlink($filePath);

        return $this->success(data: $awardOrder, message: 'Mükafat əmri uğurla yeniləndi');
    }

    public function show($awardOrder): JsonResponse
    {
        $awardOrder = AwardOrder::query()->with(['company'])->find($awardOrder);

        if (!$awardOrder) {
            return $this->error(message: "Mükafat əmri tapılmadı", code: 404);
        }

        return $this->success(data: $awardOrder);
    }

    private function getCompany($companyId): Builder|array|Collection|Model
    {
        return Company::query()->with(['mainUser', 'director'])->find($companyId);
    }

    private function templateProcessor(TemplateProcessor $templateProcessor, $filePath, $data): void
    {
        $templateProcessor->setValue('company_tax_id_number', $data['tax_id_number']);
        $templateProcessor->setValue('company_name', $data['company_name']);
        $templateProcessor->setValue('order_number', $data['order_number']);
        $templateProcessor->setValue('order_date', $data['order_date'] . $data['last_char_od']);
        $templateProcessor->setValue('d_name', $data['d_name']);
        $templateProcessor->setValue('d_surname', $data['d_surname']);
        $templateProcessor->setValue('d_father_name', $data['d_father_name']);
        $templateProcessor->setValue('main_part_of_order', $data['main_part_of_order']);
        $templateProcessor->cloneRowAndSetValues('position', $data['worker_infos']);
        $templateProcessor->saveAs($filePath);
    }

    public function destroy($awardOrder): JsonResponse
    {
        $awardOrder = AwardOrder::query()->find($awardOrder);

        if (!$awardOrder) {
            return $this->error(message: "Mükafat əmri tapılmadı", code: 404);
        }

        $awardOrderCurrentFile = $awardOrder->generated_file ?? [];

        $s3 = AWS::createClient('s3');

        $getObject = $s3->listObjects([
            'Bucket' => $awardOrderCurrentFile[0]['bucket'],
            'Key' => $awardOrderCurrentFile[0]['generated_name']
        ]);

        if (is_array($getObject['Contents']) && count($getObject['Contents']) > 0) {
            $s3->deleteObject(array(
                'Bucket' => $awardOrderCurrentFile[0]['bucket'],
                'Key' => $awardOrderCurrentFile[0]['generated_name']
            ));
        }

        $awardOrder->delete();

        return $this->success(message: "Mükafat əmri uğurla silindi");
    }
}

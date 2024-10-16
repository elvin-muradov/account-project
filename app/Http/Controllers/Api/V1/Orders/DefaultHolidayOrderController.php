<?php

namespace App\Http\Controllers\Api\V1\Orders;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Orders\DefaultHolidayOrder\DefaultHolidayOrderStore;
use App\Http\Requests\Api\V1\Orders\DefaultHolidayOrder\DefaultHolidayOrderUpdate;
use App\Http\Resources\Api\V1\Orders\DefaultHolidayOrders\DefaultHolidayOrderCollection;
use App\Http\Resources\Api\V1\Orders\DefaultHolidayOrders\DefaultHolidayOrderResource;
use App\Models\Company\AttendanceLog;
use App\Models\Company\Company;
use App\Models\Employee;
use App\Models\Orders\DefaultHolidayOrder;
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

class DefaultHolidayOrderController extends Controller
{
    use HttpResponses;

    public function index(Request $request): JsonResponse
    {
        $defaultHolidayOrders = DefaultHolidayOrder::query()
            ->with('company')
            ->paginate($request->input('limit') ?? 10);

        return $this->success(data: new DefaultHolidayOrderCollection($defaultHolidayOrders));
    }

    /**
     * @throws CopyFileException
     * @throws CreateTemporaryFileException
     */
    public function store(DefaultHolidayOrderStore $request): JsonResponse
    {
        $data = $request->validated();
        $company = $this->getCompany($request->input('company_id'));
        $companyName = $company->company_name;
        $employee = Employee::query()->with('company')->find($request->input('employee_id'));

        $orderNumber = generateOrderNumber(DefaultHolidayOrder::class, $company->company_short_name);
        $holidayStartDate = Carbon::parse($request->input('holiday_start_date'))->format('d.m.Y');
        $holidayEndDate = Carbon::parse($request->input('holiday_end_date'))->format('d.m.Y');
        $employmentStartDate = Carbon::parse($request->input('employment_start_date'))->format('d.m.Y');

        $attendanceLogs = AttendanceLog::query()
            ->where('company_id', '=', $request->input('company_id'))
            ->where('employee_id', '=', $request->input('employee_id'))
            ->whereBetween('year', [Carbon::parse($request->input('holiday_start_date'))->year,
                Carbon::parse($request->input('holiday_end_date'))->year])
            ->whereBetween('month', [Carbon::parse($request->input('holiday_start_date'))->month,
                Carbon::parse($request->input('holiday_end_date'))->month])
            ->get();

        dd($attendanceLogs);

        return $this->success(data: $attendanceLogs);

        $gender = getGender($employee->gender);
        $charHS = substr($holidayStartDate, '-2');
        $charHE = substr($holidayEndDate, '-2');
        $charES = substr($employmentStartDate, '-2');

        $lastCharHS = getNumberEnd($charHS);
        $lastCharHE = getNumberEnd($charHE);
        $lastCharES = getNumberEnd($charES);

        $data = array_merge($data, [
            'order_number' => $orderNumber,
            'name' => $employee->name,
            'surname' => $employee->surname,
            'father_name' => $employee->father_name,
            'position' => $employee->position?->name,
            'tax_id_number' => $company->tax_id_number,
            'last_char_hs' => $lastCharHS,
            'last_char_he' => $lastCharHE,
            'last_char_es' => $lastCharES,
            'company_name' => $companyName,
            'gender' => $gender,
            'holiday_start_date' => $holidayStartDate,
            'holiday_end_date' => $holidayEndDate,
            'employment_start_date' => $employmentStartDate,
            'd_name' => $company->director?->name,
            'd_surname' => $company->director?->surname,
            'd_father_name' => $company->director?->father_name,
        ]);

        $documentPath = public_path('assets/order_templates/DEFAULT_HOLIDAY.docx');
        $fileName = 'DEFAULT_HOLIDAY_ORDER_' . Str::slug($companyName . $orderNumber, '_') . '.docx';
        $filePath = public_path('assets/default_holiday_orders/' . $fileName);

        $templateProcessor = new TemplateProcessor($documentPath);
        $this->templateProcessor($templateProcessor, $filePath, $data);

        $defaultHolidayOrder = DefaultHolidayOrder::query()->create([
            'order_number' => $orderNumber,
            'company_id' => $request->input('company_id'),
            'employee_id' => $request->input('employee_id'),
            'company_name' => $companyName,
            'tax_id_number' => $company->tax_id_number,
            'name' => $employee->name,
            'surname' => $employee->surname,
            'father_name' => $employee->father_name,
            'position' => $employee->position?->name,
            'gender' => $employee->gender,
            'days_count' => $request->input('days_count'),
            'holiday_start_date' => $request->input('holiday_start_date'),
            'holiday_end_date' => $request->input('holiday_end_date'),
            'employment_start_date' => $request->input('employment_start_date'),
            'd_name' => $company->director?->name,
            'd_surname' => $company->director?->surname,
            'd_father_name' => $company->director?->father_name,
            'main_part_of_order' => $request->input('main_part_of_order')
        ]);

        $generatedFilePath = returnOrderFile($filePath, $fileName, 'defauly_holiday_orders');

        $defaultHolidayOrder->update([
            'generated_file' => $generatedFilePath
        ]);

        unlink($filePath);

        return $this->success(data: $defaultHolidayOrder, message: 'Məzuniyyət əmri uğurla yaradıldı');
    }

    /*
     * @throws CopyFileException
     * @throws CreateTemporaryFileException
     */
    public function update(DefaultHolidayOrderUpdate $request, $defaultHolidayOrder): JsonResponse
    {
        $data = $request->validated();
        $defaultHolidayOrder = DefaultHolidayOrder::query()->find($defaultHolidayOrder);

        if (!$defaultHolidayOrder) {
            return $this->error(message: 'Məzuniyyət əmri tapılmadı', code: 404);
        }

        $orderNumber = $defaultHolidayOrder->order_number;
        $company = $this->getCompany($request->input('company_id'));
        $companyName = $company->company_name;
        $employee = Employee::query()->with('position')->find($request->input('employee_id'));

        $holidayStartDate = Carbon::parse($request->input('holiday_start_date'))->format('d.m.Y');
        $holidayEndDate = Carbon::parse($request->input('holiday_end_date'))->format('d.m.Y');
        $employmentStartDate = Carbon::parse($request->input('employment_start_date'))->format('d.m.Y');

        $gender = getGender($employee->gender);

        $charHE = substr($holidayEndDate, '-2');
        $charES = substr($employmentStartDate, '-2');
        $charHS = substr($holidayStartDate, '-2');

        $lastCharHS = getNumberEnd($charHS);
        $lastCharHE = getNumberEnd($charHE);
        $lastCharES = getNumberEnd($charES);

        $data = array_merge($data, [
            'order_number' => $orderNumber,
            'name' => $employee->name,
            'surname' => $employee->surname,
            'father_name' => $employee->father_name,
            'position' => $employee->position?->name,
            'tax_id_number' => $company->tax_id_number,
            'last_char_hs' => $lastCharHS,
            'last_char_he' => $lastCharHE,
            'last_char_es' => $lastCharES,
            'company_name' => $companyName,
            'gender' => $gender,
            'holiday_start_date' => $holidayStartDate,
            'holiday_end_date' => $holidayEndDate,
            'employment_start_date' => $employmentStartDate,
            'd_name' => $company->director?->name,
            'd_surname' => $company->director?->surname,
            'd_father_name' => $company->director?->father_name
        ]);

        $documentPath = public_path('assets/order_templates/DEFAULT_HOLIDAY.docx');
        $fileName = 'DEFAULT_HOLIDAY_ORDER_' . Str::slug($companyName . $orderNumber, '_') . '.docx';
        $filePath = public_path('assets/default_holiday_orders/' . $fileName);
        $templateProcessor = new TemplateProcessor($documentPath);
        $this->templateProcessor($templateProcessor, $filePath, $data);

        $defaultHolidayOrderCurrentFile = $defaultHolidayOrder->generated_file ?? [];

        $s3 = AWS::createClient('s3');
        $s3->deleteObject(array(
            'Bucket' => $defaultHolidayOrderCurrentFile[0]['bucket'],
            'Key' => $defaultHolidayOrderCurrentFile[0]['generated_name']
        ));

        $generatedFilePath = returnOrderFile($filePath, $fileName, 'default_holiday_orders');

        $defaultHolidayOrder->update([
            'company_id' => $request->input('company_id'),
            'company_name' => $companyName,
            'tax_id_number' => $company->tax_id_number,
            'name' => $employee->name,
            'surname' => $employee->surname,
            'father_name' => $employee->father_name,
            'position' => $employee->position?->name,
            'gender' => $employee->gender,
            'days_count' => $request->input('days_count'),
            'holiday_start_date' => $request->input('holiday_start_date'),
            'holiday_end_date' => $request->input('holiday_end_date'),
            'employment_start_date' => $request->input('employment_start_date'),
            'd_name' => $company->director?->name,
            'd_surname' => $company->director?->surname,
            'd_father_name' => $company->director?->father_name,
            'main_part_of_order' => $request->input('main_part_of_order'),
            'generated_file' => $generatedFilePath
        ]);

        unlink($filePath);

        return $this->success(data: $defaultHolidayOrder, message: 'Məzuniyyət əmri uğurla yeniləndi');
    }


    public function show($defaultHolidayOrder): JsonResponse
    {
        $defaultHolidayOrder = DefaultHolidayOrder::query()->with('company')->find($defaultHolidayOrder);

        if (!$defaultHolidayOrder) {
            return $this->error(message: 'Məzuniyyət əmri tapılmadı', code: 404);
        }

        return $this->success(data: DefaultHolidayOrderResource::make($defaultHolidayOrder));
    }

    private function getCompany($companyId): Builder|array|Collection|Model
    {
        return Company::query()->with(['mainUser', 'director'])->find($companyId);
    }

    private function templateProcessor(TemplateProcessor $templateProcessor, $filePath, $data): void
    {
        $templateProcessor->setValue('company_name', $data['company_name']);
        $templateProcessor->setValue('company_tax_id_number', $data['tax_id_number']);
        $templateProcessor->setValue('order_number', $data['order_number']);
        $templateProcessor->setValue('name', $data['name']);
        $templateProcessor->setValue('surname', $data['surname']);
        $templateProcessor->setValue('father_name', $data['father_name']);
        $templateProcessor->setValue('gender', $data['gender']);
        $templateProcessor->setValue('position', $data['position']);
        $templateProcessor->setValue('days_count', $data['days_count']);
        $templateProcessor->setValue('employment_start_date', $data['employment_start_date'] . $data['last_char_es']);
        $templateProcessor->setValue('holiday_start_date', $data['holiday_start_date']);
        $templateProcessor->setValue('last_char_hs', $data['last_char_hs']);
        $templateProcessor->setValue('holiday_end_date', $data['holiday_end_date'] . $data['last_char_he']);
        $templateProcessor->setValue('d_name', $data['d_name']);
        $templateProcessor->setValue('d_surname', $data['d_surname']);
        $templateProcessor->setValue('d_father_name', $data['d_father_name']);
        $templateProcessor->setValue('main_part_of_order', $data['main_part_of_order']);
        $templateProcessor->saveAs($filePath);
    }

    public function destroy($defaultHolidayOrder): JsonResponse
    {
        $defaultHolidayOrder = DefaultHolidayOrder::query()->find($defaultHolidayOrder);

        if (!$defaultHolidayOrder) {
            return $this->error(message: 'Məzuniyyət əmri tapılmadı', code: 404);
        }

        $defaultHolidayOrderCurrentFile = $defaultHolidayOrder->generated_file ?? [];

        $s3 = AWS::createClient('s3');

        $getObject = $s3->listObjects([
            'Bucket' => $defaultHolidayOrderCurrentFile[0]['bucket'],
            'Key' => $defaultHolidayOrderCurrentFile[0]['generated_name']
        ]);

        if (is_array($getObject['Contents']) && count($getObject['Contents']) > 0) {
            $s3->deleteObject(array(
                'Bucket' => $defaultHolidayOrderCurrentFile[0]['bucket'],
                'Key' => $defaultHolidayOrderCurrentFile[0]['generated_name']
            ));
        }

        $defaultHolidayOrder->delete();

        return $this->success(message: 'Məzuniyyət əmri uğurla silindi');
    }
}

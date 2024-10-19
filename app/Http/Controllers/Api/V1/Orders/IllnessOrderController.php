<?php

namespace App\Http\Controllers\Api\V1\Orders;

use App\Enums\AttendanceLogDayTypes;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Orders\IllnessOrder\IllnessOrderStoreRequest;
use App\Http\Requests\Api\V1\Orders\IllnessOrder\IllnessOrderUpdateRequest;
use App\Http\Resources\Api\V1\Orders\IllnessOrders\IllnessOrderCollection;
use App\Http\Resources\Api\V1\Orders\IllnessOrders\IllnessOrderResource;
use App\Models\Company\AttendanceLog;
use App\Models\Company\AttendanceLogConfig;
use App\Models\Company\Company;
use App\Models\Employee;
use App\Models\Orders\IllnessOrder;
use App\Traits\HttpResponses;
use Aws\Laravel\AwsFacade as AWS;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use PhpOffice\PhpWord\Exception\CopyFileException;
use PhpOffice\PhpWord\Exception\CreateTemporaryFileException;
use PhpOffice\PhpWord\TemplateProcessor;

class IllnessOrderController extends Controller
{
    use HttpResponses;

    public function index(Request $request): JsonResponse
    {
        $orders = IllnessOrder::query()
            ->with('company')
            ->paginate($request->input('limit') ?? 10);

        return $this->success(data: new IllnessOrderCollection($orders));
    }

    /**
     * @throws CopyFileException
     * @throws CreateTemporaryFileException
     */
    public function store(IllnessOrderStoreRequest $request): JsonResponse
    {
        $data = $request->validated();
        $company = $this->getCompany($request->input('company_id'));
        $companyName = $company->company_name;
        $employee = Employee::query()->with('position')->find($request->input('employee_id'));

        $orderNumber = generateOrderNumber(IllnessOrder::class, $company->company_short_name);
        $holidayStartDate = Carbon::parse($request->input('holiday_start_date'))
            ->format('d.m.Y');
        $holidayEndDate = Carbon::parse($request->input('holiday_end_date'))
            ->format('d.m.Y');
        $employmentStartDate = Carbon::parse($request->input('employment_start_date'))->format('d.m.Y');

        $startYear = Carbon::parse($request->input('holiday_start_date'))->format('Y');
        $startMonth = Carbon::parse($request->input('holiday_start_date'))->format('n');

        $endYear = Carbon::parse($request->input('holiday_end_date'))->format('Y');
        $endMonth = Carbon::parse($request->input('holiday_end_date'))->format('n');

        DB::beginTransaction();

        $existsAttendanceLog = AttendanceLog::query()
            ->where('company_id', $request->input('company_id'))
            ->where('employee_id', $request->input('employee_id'))
            ->where('year', $startYear)
            ->where('month', $startMonth)
            ->first();

        if (!$existsAttendanceLog) {
            return $this->error(message: 'İşçi tabeldə mövcud deyil', code: 404);
        }

        $attendanceLogs = AttendanceLog::query()
            ->where('company_id', $request->input('company_id'))
            ->where('employee_id', $request->input('employee_id'))
            ->whereBetween('year', [$startYear, $endYear])
            ->whereBetween('month', [$startMonth, $endMonth])
            ->get();

        foreach ($attendanceLogs as $log) {
            $monthDays = [];

            foreach ($log->days as $k => $day) {
                $dayDate = sprintf('%s-%02d-%02d', $log->year, $log->month, $day['day']);

                if ($dayDate >= $request->holiday_start_date && $dayDate <= $request->holiday_end_date) {
                    if ($day['status'] == AttendanceLogDayTypes::NULL_DAY->value) {
                        DB::rollBack();

                        return $this->error(
                            message: "Əmək qabiliyyətinin itirilməsi tarixi aralığı tabel üzrə düzgün qeyd olunmayıb",
                            code: 400);
                    }

                    $day['status'] = AttendanceLogDayTypes::ILLNESS->value;

                    $monthDays[] = $day;
                } else {
                    $monthDays[] = $log->days[$k];
                }
            }

            $countMonthWorkDayHours = getMonthWorkDayHours($monthDays);
            $countCelebrationRestDays = getCelebrationRestDaysCount($monthDays);
            $countMonthWorkDays = getMonthWorkDaysCount($monthDays);

            $log->update([
                'salary' => $employee->salary,
                'days' => $monthDays,
                'month_work_days' => $countMonthWorkDays,
                'celebration_days' => $countCelebrationRestDays,
                'month_work_day_hours' => $countMonthWorkDayHours,
            ]);
        }

        $gender = getGender($employee->gender);

        $data = array_merge($data, [
            'order_number' => $orderNumber,
            'name' => $employee->name,
            'surname' => $employee->surname,
            'position' => $employee->position?->name,
            'father_name' => $employee->father_name,
            'company_name' => $companyName,
            'tax_id_number' => $company->tax_id_number,
            'gender' => $gender,
            'holiday_start_date' => $holidayStartDate,
            'holiday_end_date' => $holidayEndDate,
            'employment_start_date' => $employmentStartDate,
            'd_name' => $company->director?->name,
            'd_surname' => $company->director?->surname,
            'd_father_name' => $company->director?->father_name,
        ]);


        $documentPath = public_path('assets/order_templates/ILLNESS_HOLIDAY.docx');
        $fileName = 'ILLNESS_ORDER_' . Str::slug($companyName . $orderNumber, '_') . '.docx';
        $filePath = public_path('assets/illness_orders/' . $fileName);

        $templateProcessor = new TemplateProcessor($documentPath);
        $this->templateProcessor($templateProcessor, $filePath, $data);

        $illnessOrder = IllnessOrder::query()->create([
            'order_number' => $orderNumber,
            'company_id' => $request->input('company_id'),
            'employee_id' => $request->input('employee_id'),
            'company_name' => $companyName,
            'tax_id_number' => $company->tax_id_number,
            'name' => $employee->name,
            'position' => $employee->position?->name,
            'surname' => $employee->surname,
            'father_name' => $employee->father_name,
            'gender' => $employee->gender,
            'type_of_holiday' => $request->input('type_of_holiday'),
            'holiday_start_date' => $request->input('holiday_start_date'),
            'holiday_end_date' => $request->input('holiday_end_date'),
            'employment_start_date' => $request->input('employment_start_date'),
            'd_name' => $company->director?->name,
            'd_surname' => $company->director?->surname,
            'd_father_name' => $company->director?->father_name,
            'main_part_of_order' => $request->input('main_part_of_order')
        ]);

        $generatedFilePath = returnOrderFile($filePath, $fileName, 'illness_orders');

        $illnessOrder->update([
            'generated_file' => $generatedFilePath
        ]);

        unlink($filePath);

        DB::commit();

        return $this->success(data: $illnessOrder,
            message: 'Əmək qabiliyyətinin itirilməsinə görə əmr uğurla yaradıldı');
    }

    /**
     * @throws CopyFileException
     * @throws CreateTemporaryFileException
     */
    public function update(IllnessOrderUpdateRequest $request, $illnessOrder): JsonResponse
    {
        $data = $request->validated();
        $illnessOrder = IllnessOrder::query()->find($illnessOrder);

        if (!$illnessOrder) {
            return $this->error(message: 'Məzuniyyət əmri tapılmadı', code: 404);
        }

        $orderNumber = $illnessOrder->order_number;
        $company = $this->getCompany($request->input('company_id'));
        $companyName = $company->company_name;
        $employee = Employee::query()->with('position')->find($illnessOrder->employee_id);

        $holidayStartDate = Carbon::parse($request->input('holiday_start_date'))->format('d.m.Y');
        $holidayEndDate = Carbon::parse($request->input('holiday_end_date'))->format('d.m.Y');
        $employmentStartDate = Carbon::parse($request->input('employment_start_date'))->format('d.m.Y');

        $gender = getGender($employee->gender);

        $data = array_merge($data, [
            'order_number' => $orderNumber,
            'name' => $employee->name,
            'surname' => $employee->surname,
            'father_name' => $employee->father_name,
            'position' => $employee->position?->name,
            'company_name' => $companyName,
            'gender' => $gender,
            'holiday_start_date' => $holidayStartDate,
            'holiday_end_date' => $holidayEndDate,
            'employment_start_date' => $employmentStartDate,
            'tax_id_number' => $company->tax_id_number,
            'd_name' => $company->director?->name,
            'd_surname' => $company->director?->surname,
            'd_father_name' => $company->director?->father_name
        ]);

        $documentPath = public_path('assets/order_templates/ILLNESS_HOLIDAY.docx');
        $fileName = 'ILLNESS_ORDER_' . Str::slug($companyName . $orderNumber, '_') . '.docx';
        $filePath = public_path('assets/illness_orders/' . $fileName);
        $templateProcessor = new TemplateProcessor($documentPath);
        $this->templateProcessor($templateProcessor, $filePath, $data);

        $illnessOrderCurrentFile = $illnessOrder->generated_file ?? [];

        $s3 = AWS::createClient('s3');
        $s3->deleteObject(array(
            'Bucket' => $illnessOrderCurrentFile[0]['bucket'],
            'Key' => $illnessOrderCurrentFile[0]['generated_name']
        ));

        $generatedFilePath = returnOrderFile($filePath, $fileName, 'illness_orders');

        $illnessOrder->update([
            'company_id' => $request->input('company_id'),
            'company_name' => $companyName,
            'tax_id_number' => $company->tax_id_number,
            'name' => $employee->name,
            'surname' => $employee->surname,
            'father_name' => $employee->father_name,
            'position' => $employee->position?->name,
            'gender' => $employee->gender,
            'type_of_holiday' => $request->input('type_of_holiday'),
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

        return $this->success(data: $illnessOrder,
            message: 'Əmək qabiliyyətinin itirilməsinə görə əmr uğurla yeniləndi');
    }

    public function show($illnessOrder): JsonResponse
    {
        $illnessOrder = IllnessOrder::query()->with('company')->find($illnessOrder);

        if (!$illnessOrder) {
            return $this->error(message: 'Əmək qabiliyyətinin itirilməsinə görə əmr tapılmadı', code: 404);
        }

        return $this->success(data: IllnessOrderResource::make($illnessOrder));
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

    public function destroy($illnessOrder): JsonResponse
    {
        $illnessOrder = IllnessOrder::query()->find($illnessOrder);

        if (!$illnessOrder) {
            return $this
                ->error(message: 'Əmək qabiliyyətinin itirilməsinə görə əmr tapılmadı',
                    code: 404);
        }

        $illnessOrderCurrentFile = $illnessOrder->generated_file ?? [];

        $s3 = AWS::createClient('s3');
        $getObject = $s3->listObjects([
            'Bucket' => $illnessOrderCurrentFile[0]['bucket'],
            'Key' => $illnessOrderCurrentFile[0]['generated_name']
        ]);

        if (is_array($getObject['Contents']) && count($getObject['Contents']) > 0) {
            $s3->deleteObject(array(
                'Bucket' => $illnessOrderCurrentFile[0]['bucket'],
                'Key' => $illnessOrderCurrentFile[0]['generated_name']
            ));
        }

        $illnessOrder->delete();

        return $this->success(message: 'Əmək qabiliyyətinin itirilməsinə görə əmr uğurla silindi');
    }
}

<?php

namespace App\Http\Controllers\Api\V1\Orders;

use App\Enums\AttendanceLogDayTypes;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Orders\HiringOrder\HiringOrderStoreRequest;
use App\Http\Requests\Api\V1\Orders\HiringOrder\HiringOrderUpdateRequest;
use App\Http\Resources\Api\V1\Orders\HiringOrders\HiringOrderCollection;
use App\Http\Resources\Api\V1\Orders\HiringOrders\HiringOrderResource;
use App\Models\Company\AttendanceLog;
use App\Models\Company\AttendanceLogConfig;
use App\Models\Company\Company;
use App\Models\Employee;
use App\Models\Orders\HiringOrder;
use App\Traits\HttpResponses;
use Aws\Laravel\AwsFacade as AWS;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use NumberToWords\Exception\InvalidArgumentException;
use NumberToWords\Exception\NumberToWordsException;
use PhpOffice\PhpWord\Exception\CopyFileException;
use PhpOffice\PhpWord\Exception\CreateTemporaryFileException;
use PhpOffice\PhpWord\TemplateProcessor;

class HiringOrderController extends Controller
{
    use HttpResponses;

    public function index(Request $request): JsonResponse
    {
        $hiringOrders = HiringOrder::query()
            ->with('company')
            ->paginate($request->input('limit') ?? 10);

        return $this->success(data: new HiringOrderCollection($hiringOrders));
    }

    /**
     * @throws CopyFileException
     * @throws CreateTemporaryFileException
     */
    public function store(HiringOrderStoreRequest $request): JsonResponse
    {
        $data = $request->validated();

        $company = $this->getCompany($request->input('company_id'));
        $companyName = $company->company_name;
        $employee = Employee::query()->with(['position'])->find($data['employee_id']);

        $orderNumber = generateOrderNumber(HiringOrder::class, $company->company_short_name);
        $startDate = Carbon::parse($request->input('start_date'))->format('d.m.Y');
        $char = substr($startDate, '-2');
        $lastChar = getNumberEnd($char);
        $gender = !empty($employee->gender) ? getGender($employee->gender) : "MALE";

        $data = array_merge($data, [
            'position' => $employee->position?->name,
            'salary_in_words' => getNumberAsWords($data['salary']),
            'order_number' => $orderNumber,
            'last_char' => $lastChar,
            'company_name' => $companyName,
            'name' => $employee->name,
            'surname' => $employee->surname,
            'father_name' => $employee->father_name,
            'd_name' => $company->director?->name,
            'd_surname' => $company->director?->surname,
            'd_father_name' => $company->director?->father_name,
            'gender' => $gender,
            'start_date' => $startDate,
            'tax_id_number' => $company->tax_id_number
        ]);

        $year = Carbon::parse($request->input('start_date'))->format('Y');
        $month = Carbon::parse($request->input('start_date'))->format('n');
        $day = Carbon::parse($request->input('start_date'))->format('j');

        $attendanceLogConfig = AttendanceLogConfig::query()
            ->where('company_id', $request->input('company_id'))
            ->where('year', $year)
            ->first();

        if (!$attendanceLogConfig) {
            return $this->error(message: 'Tabel şablonu mövcud deyil', code: 404);
        }

        $attendanceLog = AttendanceLog::query()
            ->where('company_id', $attendanceLogConfig->company_id)
            ->where('year', $attendanceLogConfig->year)
            ->where('employee_id', $request->input('employee_id'))
            ->first();

        if ($attendanceLog) {
            return $this->error(message: 'İşçi artıq tabelə əlavə olunub', code: 404);
        }

        $config = $attendanceLogConfig->config;

        $generatedConfig = [];

        for ($i = 0; $i < count($config); $i++) {
            if ($i + 1 == $month) {
                for ($j = 0; $j < count($config[$i]['days']); $j++) {
                    if ($day > $config[$i]['days'][$j]['day']) {
                        $generatedConfig[$i]['days'][$j] = [
                            'day' => $config[$i]['days'][$j]['day'],
                            'status' => AttendanceLogDayTypes::NULL_DAY->value
                        ];
                    } else {
                        $generatedConfig[$i]['days'][$j] = [
                            'day' => $config[$i]['days'][$j]['day'],
                            'status' => $config[$i]['days'][$j]['status']
                        ];
                    }
                }
            }
        }

        $config[$month - 1]['days'] = $generatedConfig[$month - 1]['days'];

        foreach ($config as $key => $value) {
            foreach ($value['days'] as $k => $d) {
                if ($value['days'][$key]['status'] == AttendanceLogDayTypes::NULL_DAY->value) {
                    break 2;
                }

                $config[$key]['days'][$k]['status'] = AttendanceLogDayTypes::NULL_DAY->value;
            }
        }

        foreach ($config as $key => $value) {
            $countMonthWorkDayHours = getMonthWorkDayHours($config[$key]['days']);
            $countCelebrationRestDays = getCelebrationRestDaysCount($config[$key]['days']);
            $countMonthWorkDays = getMonthWorkDaysCount($config[$key]['days']);

            AttendanceLog::query()
                ->create([
                    'company_id' => $attendanceLogConfig->company_id,
                    'employee_id' => $request->input('employee_id'),
                    'year' => $attendanceLogConfig->year,
                    'month' => $value['month'],
                    'salary' => $employee->salary,
                    'days' => $value['days'],
                    'month_work_hours' => $value['month_work_hours'],
                    'month_work_days' => $countMonthWorkDays,
                    'celebration_days' => $countCelebrationRestDays,
                    'month_work_day_hours' => $countMonthWorkDayHours,
                    'start_date' => Carbon::createFromDate($year, $month, 1),
                    'end_date' => Carbon::createFromDate($year, $month, Carbon::createFromDate($year, $month, 1)
                        ->daysInMonth),
                ]);
        }

        $documentPath = public_path('assets/order_templates/HIRING.docx');
        $fileName = 'HIRING_ORDER_' . Str::slug($companyName . $orderNumber, '_') . '.docx';
        $filePath = public_path('assets/hiring_orders/' . $fileName);

        $templateProcessor = new TemplateProcessor($documentPath);
        $this->templateProcessor($templateProcessor, $filePath, $data);

        $hiringOrder = HiringOrder::query()->create([
            'order_number' => $orderNumber,
            'company_id' => $request->input('company_id'),
            'employee_id' => $request->input('employee_id'),
            'company_name' => $companyName,
            'tax_id_number' => $company->tax_id_number,
            'name' => $employee->name,
            'surname' => $employee->surname,
            'father_name' => $employee->father_name,
            'gender' => $employee->gender,
            'start_date' => $request->input('start_date'),
            'position' => $employee->position?->name,
            'salary' => $request->input('salary'),
            'salary_in_words' => getNumberAsWords($request->input('salary')),
            'd_name' => $company->director?->name,
            'd_surname' => $company->director?->surname,
            'd_father_name' => $company->director?->father_name,
        ]);

        $generatedFilePath = returnOrderFile($filePath, $fileName, 'hiring_orders');

        $hiringOrder->update([
            'generated_file' => $generatedFilePath
        ]);

        $employee->update(['salary' => $request->input('salary')]);

        unlink($filePath);

        return $this->success(data: $hiringOrder, message: 'İşə götürmə sənədi yaradıldı');
    }

    /**
     * @throws CopyFileException
     * @throws CreateTemporaryFileException
     */
    public function update(HiringOrderUpdateRequest $request, $hiringOrder): JsonResponse
    {
        $data = $request->validated();
        $hiringOrder = HiringOrder::query()->find($hiringOrder);

        if (!$hiringOrder) {
            return $this->error(message: 'İşə götürmə sənədi tapılmadı', code: 404);
        }

        $company = $this->getCompany($request->input('company_id'));
        $companyName = $company->company_name;
        $employee = Employee::query()->with(['position'])->find($request->input('employee_id'));
        $orderNumber = $hiringOrder->order_number;
        $startDate = Carbon::parse($request->input('start_date'))->format('d.m.Y');
        $char = substr($startDate, '-2');
        $lastChar = getNumberEnd($char);
        $gender = getGender($employee->gender);

        $data = array_merge($data, [
            'order_number' => $orderNumber,
            'position' => $employee->position?->name,
            'salary' => $request->input('salary'),
            'salary_in_words' => getNumberAsWords($request->input('salary')),
            'last_char' => $lastChar,
            'company_name' => $companyName,
            'name' => $employee->name,
            'surname' => $employee->surname,
            'father_name' => $employee->father_name,
            'd_name' => $company->director?->name,
            'd_surname' => $company->director?->surname,
            'd_father_name' => $company->director?->father_name,
            'gender' => $gender,
            'start_date' => $startDate,
            'tax_id_number' => $company->tax_id_number,
        ]);

        $year = Carbon::parse($request->input('start_date'))->format('Y');
        $month = Carbon::parse($request->input('start_date'))->format('n');
        $day = Carbon::parse($request->input('start_date'))->format('j');

        $attendanceLogConfig = AttendanceLogConfig::query()
            ->where('company_id', $request->input('company_id'))
            ->where('year', $year)
            ->where('month', $month)
            ->first();

        if (!$attendanceLogConfig) {
            return $this->error(message: 'Tabel şablonu mövcud deyil', code: 404);
        }

        $attendanceLog = AttendanceLog::query()
            ->where('company_id', $attendanceLogConfig->company_id)
            ->where('year', $attendanceLogConfig->year)
            ->where('month', $attendanceLogConfig->month)
            ->where('employee_id', $request->input('employee_id'))
            ->first();

        $attendanceLog?->delete();

        $config = $attendanceLogConfig->config;

        for ($i = 0; $i < $day - 1; $i++) {
            $config[$i] = [
                'day' => $i + 1,
                'status' => 'NULL_DAY',
            ];
        }

        $countMonthWorkDayHours = getMonthWorkDayHours($config);
        $countCelebrationRestDays = getCelebrationRestDaysCount($config);
        $countMonthWorkDays = getMonthWorkDaysCount($config);

        AttendanceLog::query()
            ->create([
                'company_id' => $attendanceLogConfig->company_id,
                'employee_id' => $request->input('employee_id'),
                'year' => $attendanceLogConfig->year,
                'month' => $attendanceLogConfig->month,
                'days' => $config,
                'month_work_days' => $countMonthWorkDays,
                'celebration_days' => $countCelebrationRestDays,
                'month_work_day_hours' => $countMonthWorkDayHours,
            ]);

        $documentPath = public_path('assets/order_templates/HIRING.docx');
        $fileName = 'HIRING_ORDER_' . Str::slug($companyName . $orderNumber, '_') . '.docx';
        $filePath = public_path('assets/hiring_orders/' . $fileName);
        $templateProcessor = new TemplateProcessor($documentPath);
        $this->templateProcessor($templateProcessor, $filePath, $data);

        $hiringOrderCurrentFile = $hiringOrder->generated_file ?? [];
        $s3 = AWS::createClient('s3');
        $s3->deleteObject(array(
            'Bucket' => $hiringOrderCurrentFile[0]['bucket'],
            'Key' => $hiringOrderCurrentFile[0]['generated_name']
        ));

        $generatedFilePath = returnOrderFile($filePath, $fileName, 'hiring_orders');

        $hiringOrder->update([
            'company_id' => $request->input('company_id'),
            'company_name' => $companyName,
            'tax_id_number' => $company->tax_id_number,
            'name' => $employee->name,
            'surname' => $employee->surname,
            'father_name' => $employee->father_name,
            'gender' => $employee->gender,
            'start_date' => $request->input('start_date'),
            'position' => $employee->position?->name,
            'salary' => $request->input('salary'),
            'salary_in_words' => getNumberAsWords($request->input('salary')),
            'd_name' => $company->director?->name,
            'd_surname' => $company->director?->surname,
            'd_father_name' => $company->director?->father_name,
            'generated_file' => $generatedFilePath
        ]);

        $employee->update(['salary' => $request->input('salary')]);

        unlink($filePath);

        return $this->success(data: $hiringOrder, message: 'İşə götürmə sənədi uğurla yeniləndi');
    }

    public function show($hiringOrder): JsonResponse
    {
        $hiringOrder = HiringOrder::query()->with('company')->find($hiringOrder);

        if (!$hiringOrder) {
            return $this->error(message: 'İşə götürmə sənədi tapılmadı', code: 404);
        }

        return $this->success(data: HiringOrderResource::make($hiringOrder), message: 'İşə götürmə sənədi tapıldı');
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
        $templateProcessor->setValue('position', $data['position']);
        $templateProcessor->setValue('salary', $data['salary']);
        $templateProcessor->setValue('salary_in_words', $data['salary_in_words']);
        $templateProcessor->setValue('start_date', $data['start_date'] . $data['last_char']);
        $templateProcessor->setValue('name', $data['name']);
        $templateProcessor->setValue('surname', $data['surname']);
        $templateProcessor->setValue('father_name', $data['father_name']);
        $templateProcessor->setValue('gender', $data['gender']);
        $templateProcessor->setValue('d_surname', $data['d_surname']);
        $templateProcessor->setValue('d_name', $data['d_name']);
        $templateProcessor->setValue('d_father_name', $data['d_father_name']);
        $templateProcessor->saveAs($filePath);
    }

    public function destroy($hiringOrder): JsonResponse
    {
        $hiringOrder = HiringOrder::query()->find($hiringOrder);

        if (!$hiringOrder) {
            return $this->error(message: 'İşə götürmə sənədi tapılmadı', code: 404);
        }

        $hiringOrderCurrentFile = $hiringOrder->generated_file ?? [];
        $s3 = AWS::createClient('s3');

        $getObject = $s3->listObjects([
            'Bucket' => $hiringOrderCurrentFile[0]['bucket'],
            'Key' => $hiringOrderCurrentFile[0]['generated_name']
        ]);

        if (is_array($getObject['Contents']) && count($getObject['Contents']) > 0) {
            $s3->deleteObject(array(
                'Bucket' => $hiringOrderCurrentFile[0]['bucket'],
                'Key' => $hiringOrderCurrentFile[0]['generated_name']
            ));
        }

        $hiringOrder->delete();

        return $this->success(message: 'İşə götürmə sənədi uğurla silindi');
    }
}

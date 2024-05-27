<?php

namespace App\Http\Controllers\Api\V1\Companies;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\Companies\AttendanceLog\AttendanceLogCollection;
use App\Http\Resources\Api\V1\Companies\AttendanceLog\AttendanceLogResource;
use App\Models\Company\AttendanceLog;
use App\Models\Company\AttendanceLogConfig;
use App\Models\Employee;
use App\Traits\HttpResponses;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AttendanceLogController extends Controller
{
    use HttpResponses;

    public function index(Request $request): JsonResponse
    {
        $attendanceLogs = AttendanceLog::query()
            ->with([
                'company:id,company_name',
                'employee:id,name,surname,position_id',
                'employee.position:id,name'
            ])
            ->paginate($request->input('limit') ?? 10);

        return $this->success(data: new AttendanceLogCollection($attendanceLogs));
    }

    public function show($attendanceLog): JsonResponse
    {
        $attendanceLog = AttendanceLog::query()
            ->with([
                'company:id,company_name',
                'employee:id,name,surname,position_id',
                'employee.position:id,name'
            ])->find($attendanceLog);

        if (!$attendanceLog) {
            return $this->error(message: "Tabel məlumatı tapılmadı", code: 404);
        }

        return $this->success(data: AttendanceLogResource::make($attendanceLog));
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'attendance_log_config_id' => ['required', 'integer', Rule::exists('attendance_log_configs', 'id')
                ->where(function ($query) use ($request) {
                    $query->where('company_id', $request->input('company_id'));
                })],
            'employee_id' => ['required', 'integer', Rule::exists('employees', 'id')
                ->where(function ($query) use ($request) {
                    $query->where('company_id', $request->input('company_id'));
                })],
        ]);

        $attendaceLogConfig = AttendanceLogConfig::query()->find($request->input('attendance_log_config_id'));

        $request->validate([
            'employee_id' => [Rule::unique('attendance_logs', 'employee_id')
                ->where(function ($query) use ($attendaceLogConfig) {
                    $query->where('company_id', $attendaceLogConfig->company_id)
                        ->where('year', $attendaceLogConfig->year)
                        ->where('month', $attendaceLogConfig->month);
                })]
        ]);

        $countMonthWorkDayHours = getMonthWorkDayHours($attendaceLogConfig->config);
        $countCelebrationRestDays = getCelebrationRestDaysCount($attendaceLogConfig->config);
        $countMonthWorkDays = getMonthWorkDaysCount($attendaceLogConfig->config);

        $attendanceLog = new AttendanceLog();
        $attendanceLog->company_id = $attendaceLogConfig->company_id;
        $attendanceLog->employee_id = $request->input('employee_id');
        $attendanceLog->year = $attendaceLogConfig->year;
        $attendanceLog->month = $attendaceLogConfig->month;
        $attendanceLog->days = $attendaceLogConfig->config;
        $attendanceLog->month_work_days = $countMonthWorkDays;
        $attendanceLog->celebration_days = $countCelebrationRestDays;
        $attendanceLog->month_work_day_hours = $countMonthWorkDayHours;
        $attendanceLog->save();

        return $this->success(data: new AttendanceLogResource($attendanceLog),
            message: "İşçi tabelə uğurla əlavə olundu", code: 201);
    }

    public function update(Request $request, $attendanceLog): JsonResponse
    {
        $request->validate([
            'attendance_log_config_id' => ['required', 'integer', Rule::exists('attendance_log_configs', 'id')
                ->where(function ($query) use ($request) {
                    $query->where('company_id', $request->input('company_id'));
                })],
            'employee_id' => ['required', 'integer', Rule::exists('employees', 'id')
                ->where(function ($query) use ($request) {
                    $query->where('company_id', $request->input('company_id'));
                })],
        ]);

        $attendaceLogConfig = AttendanceLogConfig::query()->find($request->input('attendance_log_config_id'));

        $attendanceLog = AttendanceLog::query()->find($attendanceLog);

        if (!$attendanceLog) {
            return $this->error(message: "Tabel məlumatı tapılmadı", code: 404);
        }

        $request->validate([
            'employee_id' => [Rule::unique('attendance_logs', 'employee_id')
                ->where(function ($query) use ($attendaceLogConfig) {
                    $query->where('company_id', $attendaceLogConfig->company_id)
                        ->where('year', $attendaceLogConfig->year)
                        ->where('month', $attendaceLogConfig->month);
                })->ignore($attendanceLog->id)]
        ]);

        $countMonthWorkDayHours = getMonthWorkDayHours($attendaceLogConfig->config);
        $countCelebrationRestDays = getCelebrationRestDaysCount($attendaceLogConfig->config);
        $countMonthWorkDays = getMonthWorkDaysCount($attendaceLogConfig->config);

        $attendanceLog = new AttendanceLog();
        $attendanceLog->company_id = $attendaceLogConfig->company_id;
        $attendanceLog->employee_id = $request->input('employee_id');
        $attendanceLog->year = $attendaceLogConfig->year;
        $attendanceLog->month = $attendaceLogConfig->month;
        $attendanceLog->days = $attendaceLogConfig->config;
        $attendanceLog->month_work_days = $countMonthWorkDays;
        $attendanceLog->celebration_days = $countCelebrationRestDays;
        $attendanceLog->month_work_day_hours = $countMonthWorkDayHours;
        $attendanceLog->save();

        return $this->success(data: new AttendanceLogResource($attendanceLog),
            message: "İşçi tabelə uğurla əlavə olundu", code: 201);
    }

    public function destroy($attendanceLog): JsonResponse
    {
        $attendanceLog = AttendanceLog::query()->find($attendanceLog);

        if (!$attendanceLog) {
            return $this->error(message: "Tabel məlumatı tapılmadı", code: 404);
        }

        $attendanceLog->delete();

        return $this->success(message: "Tabel məlumatı ugurla silindi");
    }
}

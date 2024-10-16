<?php

namespace App\Http\Controllers\Api\V1\Companies;

use App\Enums\AttendanceLogConfigDayTypes;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\Companies\AttendanceLogConfig\AttendanceLogConfigCollection;
use App\Http\Resources\Api\V1\Companies\AttendanceLogConfig\AttendanceLogConfigResource;
use App\Models\Company\AttendanceLogConfig;
use App\Traits\HttpResponses;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AttendanceLogConfigController extends Controller
{
    use HttpResponses;

    public function index(Request $request): JsonResponse
    {
        $attendanceLogConfigs = AttendanceLogConfig::query()
            ->paginate($request->input('limit') ?? 10);

        return $this->success(data: new AttendanceLogConfigCollection($attendanceLogConfigs));
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'company_id' => ['required', 'integer', 'exists:companies,id'],
            'year' => ['required', 'integer', 'between:2000,2080'],
            'month' => ['required', 'integer', 'between:1,12',
                Rule::unique('attendance_log_configs', 'month')
                    ->where('year', $request->input('year'))
                    ->where('company_id', $request->input('company_id'))
            ],
            'config' => ['required', 'array'],
            'config.*.day' => ['required', 'integer'],
            'config.*.status' => ['required', 'string', 'in:' . AttendanceLogConfigDayTypes::toString()]
        ]);

        $carbonDate = Carbon::createFromDate($request->input('year'), $request->input('month'));
        $monthDaysCount = $carbonDate->lastOfMonth()->day;
        $monthDaysList = returnMonthDaysAsArray($monthDaysCount);
        $monthDaysListEnum = implode(',', $monthDaysList);
        $checkMonthDaysUnique = checkMonthDaysUnique($monthDaysCount, $request->input('config.*.day'));

        $request->validate([
            'config.*.day' => ['required', 'integer', 'in:' . $monthDaysListEnum, 'between:1,' . $monthDaysCount],
        ]);

        if (!$checkMonthDaysUnique) {
            return $this->error(message: 'Zəhmət olmasa günləri düzgün daxil edin', code: 400);
        }

        $attendanceLogConfig = AttendanceLogConfig::query()->create([
            'company_id' => $request->input('company_id'),
            'year' => $request->input('year'),
            'month' => $request->input('month'),
            'config' => $request->input('config'),
            'log_date' => $carbonDate->format('Y-m-d'),
        ]);

        return $this
            ->success(data: AttendanceLogConfigResource::make($attendanceLogConfig),
                message: "Tabel şablonu uğurla əlavə olundu", code: 201);
    }

    public function show($attendanceLogConfig): JsonResponse
    {
        $attendanceLogConfig = AttendanceLogConfig::query()
            ->with('company:id,company_name')
            ->find($attendanceLogConfig);

        if (!$attendanceLogConfig) {
            return $this->error(message: 'Tabel şablonu tapılmadı', code: 404);
        }

        return $this->success(data: AttendanceLogConfigResource::make($attendanceLogConfig));
    }

    public function update(Request $request, $attendanceLogConfig): JsonResponse
    {
        $attendanceLogConfig = AttendanceLogConfig::query()->find($attendanceLogConfig);

        if (!$attendanceLogConfig) {
            return $this->error(message: 'Tabel şablonu tapılmadı', code: 404);
        }

        $request->validate([
            'year' => ['required', 'integer', 'between:2000,2080'],
            'month' => ['required', 'integer', 'between:1,12',
                Rule::unique('attendance_log_configs', 'month')
                    ->where('year', $request->input('year'))
                    ->where('company_id', $attendanceLogConfig->company_id)
                    ->ignore($attendanceLogConfig->id)
            ],
            'config' => ['required', 'array'],
            'config.*.day' => ['required', 'integer'],
            'config.*.status' => ['required', 'string', 'in:' . AttendanceLogConfigDayTypes::toString()]
        ]);

        $carbonDate = Carbon::createFromDate($request->input('year'), $request->input('month'));
        $monthDaysCount = $carbonDate->lastOfMonth()->day;
        $monthDaysList = returnMonthDaysAsArray($monthDaysCount);
        $monthDaysListEnum = implode(',', $monthDaysList);
        $checkMonthDaysUnique = checkMonthDaysUnique($monthDaysCount, $request->input('config.*.day'));

        $request->validate([
            'config.*.day' => ['required', 'integer', 'in:' . $monthDaysListEnum, 'between:1,' . $monthDaysCount],
        ]);

        if (!$checkMonthDaysUnique) {
            return $this->error(message: 'Eyni gün birdən artıq daxil edilə bilməz', code: 400);
        }

        $attendanceLogConfig->update([
            'company_id' => $request->input('company_id'),
            'year' => $request->input('year'),
            'month' => $request->input('month'),
            'config' => $request->input('config')
        ]);

        return $this->success(data: $attendanceLogConfig, message: "Tabel şablonu uğurla yeniləndi");
    }

    public function destroy($attendanceLogConfig): JsonResponse
    {
        $attendanceLogConfig = AttendanceLogConfig::query()->find($attendanceLogConfig);

        if (!$attendanceLogConfig) {
            return $this->error(message: 'Tabel şablonu tapılmadı', code: 404);
        }

        $attendanceLogConfig->delete();

        return $this->success(message: 'Tabel şablonu uğurla silindi');
    }
}

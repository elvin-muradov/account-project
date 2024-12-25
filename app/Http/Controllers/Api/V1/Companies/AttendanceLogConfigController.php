<?php

namespace App\Http\Controllers\Api\V1\Companies;

use App\Enums\AttendanceLogConfigDayTypes;
use App\Enums\MonthsLocale;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\Companies\AttendanceLogConfig\AttendanceLogConfigCollection;
use App\Http\Resources\Api\V1\Companies\AttendanceLogConfig\AttendanceLogConfigResource;
use App\Models\Company\AttendanceLogConfig;
use App\Traits\HttpResponses;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class AttendanceLogConfigController extends Controller
{
    use HttpResponses;

    public function index(Request $request): JsonResponse
    {
        $companyId = getHeaderCompanyId();

        if (!$companyId) {
            return $this->error(message: "Şirkət tapılmadı", code: 404);
        }

        $attendanceLogConfigs = AttendanceLogConfig::query()
            ->where('company_id', $companyId)
            ->paginate($request->input('limit') ?? 10);

        return $this->success(data: new AttendanceLogConfigCollection($attendanceLogConfigs));
    }

    public function store(Request $request): JsonResponse
    {
        $companyId = getHeaderCompanyId();

        if (!$companyId) {
            return $this->error(message: "Şirkət tapılmadı", code: 404);
        }

        $request->validate([
            'year' => ['required', 'integer', 'unique:attendance_log_configs,year', 'between:2000,2080'],
            'config' => ['required', 'array'],
            'config.*.month' => ['required', 'integer', 'between:1,12',
                Rule::unique('attendance_log_configs', 'year')
                    ->where('year', $request->input('year'))
                    ->where('company_id', $companyId)
            ],
            'config.*.days' => ['required', 'array'],
            'config.*.days.*.day' => ['required', 'integer'],
            'config.*.days.*.status' => ['required', 'in:' . AttendanceLogConfigDayTypes::toString()]
        ]);

        $yearConfig = collect(range(1, 12))->map(function ($month) use ($request) {
            return [
                'days' => collect(range(1, intval(Carbon::createFromDate($request->input('year'),
                    $month, 1)->daysInMonth)))->map(function ($day) {
                    return [
                        'day' => $day,
                    ];
                })->toArray(),
                'month' => $month,
                'month_name' => Carbon::createFromDate($request->input('year'),
                    $month, 1)->isoFormat('MMMM'),
            ];
        })->toArray();

        $checkUnique = checkMonthDaysUnique($yearConfig, $request->input('config'));

        if (gettype($checkUnique) == 'string') {
            return $this->error(message: 'Zəhmət olmasa '
                . $checkUnique .
                ' ayını düzgün daxil edin', code: 400);
        }

        $carbonDate = Carbon::create($request->input('year'), $request->input('config')[0]['month'], 1);

        $generatedConfig = [];

        foreach ($request->input('config') as $configDetail) {
            $monthWorkHours = 0;

            foreach ($configDetail['days'] as $day) {
                $monthWorkHours += intval($day['status']);
            }

            $generatedConfig[] = [
                'days' => $configDetail['days'],
                'month' => $configDetail['month'],
                'month_name' => $configDetail['month_name'],
                'month_work_hours' => $monthWorkHours
            ];
        }

        $attendanceLogConfig = AttendanceLogConfig::query()->create([
            'company_id' => $companyId,
            'year' => $request->input('year'),
            'log_date' => $carbonDate->format('Y-m-d'),
            'config' => $generatedConfig,
        ]);

        return $this
            ->success(data: AttendanceLogConfigResource::make($attendanceLogConfig),
                message: "Tabel şablonu uğurla əlavə olundu", code: 201);
    }

    public function show($attendanceLogConfig): JsonResponse
    {
        $companyId = getHeaderCompanyId();

        if (!$companyId) {
            return $this->error(message: "Şirkət tapılmadı", code: 404);
        }

        $attendanceLogConfig = AttendanceLogConfig::query()
            ->where('company_id', $companyId)
            ->with('company:id,company_name')
            ->find($attendanceLogConfig);

        if (!$attendanceLogConfig) {
            return $this->error(message: 'Tabel şablonu tapılmadı', code: 404);
        }

        return $this->success(data: AttendanceLogConfigResource::make($attendanceLogConfig));
    }

    public function update(Request $request, $attendanceLogConfig): JsonResponse
    {
        $companyId = getHeaderCompanyId();

        if (!$companyId) {
            return $this->error(message: "Şirkət tapılmadı", code: 404);
        }

        $attendanceLogConfig = AttendanceLogConfig::query()
            ->where('company_id', $companyId)
            ->find($attendanceLogConfig);

        if (!$attendanceLogConfig) {
            return $this->error(message: 'Tabel şablonu tapılmadı', code: 404);
        }

        $request->validate([
            'year' => ['required', 'integer', 'between:2000,2080',
                Rule::unique('attendance_log_configs', 'year')->ignore($attendanceLogConfig->id)],
            'config' => ['required', 'array'],
            'config.*.month' => ['required', 'integer', 'between:1,12',
                Rule::unique('attendance_log_configs', 'year')
                    ->where('year', $request->input('year'))
                    ->where('company_id', $request->input('company_id'))
                    ->ignore($attendanceLogConfig->id),
            ],
            'config.*.month_name' => ['required', 'string', 'in:' . MonthsLocale::toString()],
            'config.*.days' => ['required', 'array'],
            'config.*.days.*.day' => ['required', 'integer'],
            'config.*.days.*.status' => ['required', 'in:' . AttendanceLogConfigDayTypes::toString()]
        ]);

        $yearConfig = collect(range(1, 12))->map(function ($month) use ($request) {
            return [
                'days' => collect(range(1, intval(Carbon::createFromDate($request->input('year'),
                    $month, 1)->daysInMonth)))->map(function ($day) {
                    return [
                        'day' => $day,
                    ];
                })->toArray(),
                'month' => $month,
                'month_name' => Carbon::createFromDate($request->input('year'),
                    $month, 1)->isoFormat('MMMM'),
            ];
        })->toArray();

        $checkUnique = checkMonthDaysUnique($yearConfig, $request->input('config'));

        if (gettype($checkUnique) == 'string') {
            return $this->error(message: 'Zəhmət olmasa '
                . $checkUnique .
                ' ayını düzgün daxil edin', code: 400);
        }

        $carbonDate = Carbon::create($request->input('year'), $request->input('config')[0]['month'], 1);

        $generatedConfig = [];

        foreach ($request->input('config') as $configDetail) {
            $monthWorkHours = 0;

            foreach ($configDetail['days'] as $day) {
                $monthWorkHours += intval($day['status']);
            }

            $generatedConfig[] = [
                'days' => $configDetail['days'],
                'month' => $configDetail['month'],
                'month_name' => $configDetail['month_name'],
                'month_work_hours' => $monthWorkHours
            ];
        }

        $attendanceLogConfig->update([
            'company_id' => $companyId,
            'year' => $request->input('year'),
            'log_date' => $carbonDate->format('Y-m-d'),
            'config' => $generatedConfig,
        ]);

        return $this
            ->success(data: AttendanceLogConfigResource::make($attendanceLogConfig),
                message: "Tabel şablonu uğurla yeniləndi", code: 201);
    }

    public function destroy($attendanceLogConfig): JsonResponse
    {
        $companyId = getHeaderCompanyId();

        if (!$companyId) {
            return $this->error(message: "Şirkət tapılmadı", code: 404);
        }

        $attendanceLogConfig = AttendanceLogConfig::query()
            ->where('company_id', $companyId)
            ->find($attendanceLogConfig);

        if (!$attendanceLogConfig) {
            return $this->error(message: 'Tabel şablonu tapılmadı', code: 404);
        }

        $attendanceLogConfig->delete();

        return $this->success(message: 'Tabel şablonu uğurla silindi');
    }
}

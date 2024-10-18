<?php

namespace Database\Seeders;

use App\Models\Company\AttendanceLogConfig;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AttendanceLogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $year = 2024;
        $months = 12;

        $attendanceLogs = [];

        for ($i = 1; $i <= $months; $i++) {
            $attendanceLogs[] = [
                'month' => $i,
                'month_name' => Carbon::createFromDate($year, $i)->isoFormat('MMMM'),
                'days' => []
            ];
            for ($j = 1; $j <= Carbon::createFromDate($year, $i)->daysInMonth; $j++) {
                if (Carbon::createFromDate($year, $i, $j)->dayName === "şənbə" ||
                    Carbon::createFromDate($year, $i, $j)->dayName === 'bazar') {
                    $attendanceLogs[$i - 1]['days'][] = [
                        'day' => $j,
                        'status' => 'REST_DAY',
                    ];
                } else {
                    $attendanceLogs[$i - 1]['days'][] = [
                        'day' => $j,
                        'status' => 8,
                    ];
                }
            }
        }

        $attendanceLog = AttendanceLogConfig::query()->create([
            'company_id' => 1,
            'year' => 2024,
            'log_date' => Carbon::createFromDate($year, 1, 1),
            'config' => $attendanceLogs
        ]);
    }
}

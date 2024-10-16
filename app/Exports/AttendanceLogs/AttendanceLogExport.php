<?php

namespace App\Exports\AttendanceLogs;

use App\Models\Company\AttendanceLog;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class AttendanceLogExport implements FromView
{

    public function view(): View
    {
        $attendanceLogs = AttendanceLog::query()
            ->get();

        return view('exports.attendance_logs.attendance_log_export', [
            'attendanceLogs' => $attendanceLogs
        ]);
    }
}

<?php

namespace App\Exports\AttendanceLogs;

use App\Models\Company\AttendanceLog;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class AttendanceLogExport implements FromView
{
    private mixed $req;

    public function __construct($req)
    {
        $this->req = $req;
    }

    public function view(): View
    {
        $attendanceLogs = AttendanceLog::query()
            ->with(['employee', 'employee.position'])
            ->where('company_id', '=', $this->req['company_id'])
            ->where('year', '=', $this->req['year'])
            ->where('month', '=', $this->req['month'])
            ->get();

        return view('exports.attendance_logs.attendance_log_export', [
            'attendanceLogs' => $attendanceLogs
        ]);
    }
}

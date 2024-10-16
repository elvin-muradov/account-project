<?php

namespace App\Http\Controllers\Api\V1\Excel;

use App\Exports\AttendanceLogs\AttendanceLogExport;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class AttendanceLogExcelController extends Controller
{
    public function exportAttendanceLogExcel(Request $request): BinaryFileResponse
    {
        $request->validate([
            'company_id' => ['required', 'integer', 'exists:companies,id'],
            'month' => ['required', 'integer', 'between:1,12'],
            'year' => ['required', 'integer', 'between:1900,' . date('Y')]
        ]);

        $req = $request->only('company_id', 'month', 'year');

        return Excel::download(new AttendanceLogExport($req), 'attendance_log_export.xlsx');
    }
}

<?php

namespace App\Http\Controllers\Api\V1\Excel;

use App\Exports\Salary\SalaryCalculateExport;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class SalaryCalculateExcelController extends Controller
{
    public function exportSalaryCalculateExcel(Request $request): BinaryFileResponse
    {
        $req = $request->validate([
            'company_id' => ['required', 'integer', 'exists:companies,id'],
            'year' => ['required', 'integer'],
            'month' => ['required', 'integer'],
        ]);

        return Excel::download(new SalaryCalculateExport($req), 'salary_calculate_export.xlsx');
    }
}

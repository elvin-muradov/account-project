<?php

namespace App\Http\Controllers\Api\V1\Excel;

use App\Exports\ImportQueries\ImportCostExport;
use App\Exports\ImportQueries\ImportCostVNExport;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ImportCostExcelController extends Controller
{
    public function exportImportCostsExcel(Request $request): BinaryFileResponse
    {
        $selectedIds = $request->has('selected_ids') ? explode(',', $request->input('selected_ids')) : null;

        return Excel::download(new ImportCostExport($selectedIds), 'import_costs_export.xlsx');
    }

    public function exportImportCostsVNExcel(Request $request): BinaryFileResponse
    {
        $selectedIds = $request->has('selected_ids') ? explode(',', $request->input('selected_ids')) : null;

        return Excel::download(new ImportCostVNExport($selectedIds), 'import_costs_vn_export.xlsx');
    }
}

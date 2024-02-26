<?php

namespace App\Http\Controllers\Api\V1\Excel;

use App\Exports\ImportQueries\ImportQueryExport;
use App\Exports\ImportQueries\ImportQueryGExport;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ImportQueryExcelController extends Controller
{
    public function exportImportQueryExcel(Request $request): BinaryFileResponse
    {
        $selectedIds = $request->has('selected_ids') ? explode(',', $request->input('selected_ids')) : null;

        return Excel::download(new ImportQueryExport($selectedIds), 'import_query_export.xlsx');
    }

    public function exportImportQueryGExcel(Request $request): BinaryFileResponse
    {
        $selectedIds = $request->has('selected_ids') ? explode(',', $request->input('selected_ids')) : null;

        return Excel::download(new ImportQueryGExport($selectedIds), 'import_query_g_export.xlsx');
    }
}

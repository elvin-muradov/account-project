<?php

namespace App\Http\Controllers\Api\V1\Excel;

use App\Exports\Treasure\TreasureTransactionExport;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Maatwebsite\Excel\Facades\Excel;

class TreasureTransactionExcelController extends Controller
{
    public function exportTreasureTransactionExcel(Request $request): BinaryFileResponse
    {
        $req = $request->validate([
            'year' => ['required', 'integer'],
            'month' => ['required', 'integer'],
        ]);

        return Excel::download(new TreasureTransactionExport($req), 'treasure_transaction_export.xlsx');
    }
}

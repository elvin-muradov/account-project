<?php

namespace App\Http\Controllers\Api\V1\Queries;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Queries\ImportCost\ImportCostStoreRequest;
use App\Http\Resources\Api\V1\Queries\ImportCost\ImportCostCollection;
use App\Http\Resources\Api\V1\Queries\ImportCost\ImportCostResource;
use App\Models\Queries\ImportCost;
use App\Models\Queries\ImportCostDetail;
use App\Models\Queries\ImportQuery;
use App\Traits\HttpResponses;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ImportCostController extends Controller
{
    use HttpResponses;

    public function index(Request $request): JsonResponse
    {
        $importCosts = ImportCost::query()
            ->with(['importCostDetails', 'importQuery'])
            ->paginate($request->input('limit') ?? 10);

        return $this->success(data: new ImportCostCollection($importCosts));
    }

    public function store(ImportCostStoreRequest $request): JsonResponse
    {
        $importQuery = ImportQuery::query()
            ->with(['importQueryDetails', 'currency'])
            ->find($request->input('import_query_id'));

        if (!$importQuery) {
            return $this->error(message: "İdxal sorğusu tapılmadı", code: 404);
        }

        $totalRatio = 100;
        $totalShortImportDuty = $request->input('total_short_import_duty');
        $totalCustomsShortAndImportDuty = $request->input('total_customs_short_and_import_duty');
        $totalOtherExpenses = $request->input('total_other_expenses');
        $totalCustomsCollection = $request->input('total_customs_collection');
        $totalTransportExpenses = $request->input('total_transport_expenses');
        $totalImportFeeAndOtherExpenses = $request->input('total_import_fee_and_other_expenses');
        $totalVat = $importQuery->vat;
        $totalAmountCurrency = $importQuery->invoice_value;
        $totalAmountAZN = 0;
        $totalAmount = 0;
        $importCostDetails = [];

        foreach ($importQuery->importQueryDetails as $importQueryDetail) {
            $ratio = $importQueryDetail->subtotal_amount / $totalAmountCurrency * $totalRatio;
            $subtotalAmountAZN = $importQuery->currency->rate * $importQueryDetail->subtotal_amount
                + $ratio * $totalShortImportDuty / 100 + $ratio * $totalCustomsShortAndImportDuty / 100
                + $ratio * $totalOtherExpenses / 100 + $ratio * $totalCustomsCollection / 100
                + $ratio * $totalTransportExpenses / 100 + $ratio * $totalImportFeeAndOtherExpenses / 100;

            $totalAmountAZN += $subtotalAmountAZN;

            $importCostDetails[] = [
                'import_query_detail_id' => $importQueryDetail->id,
                'quantity' => $importQueryDetail->quantity,
                'ratio' => $ratio,
                'short_import_duty' => $ratio * $totalShortImportDuty / 100,
                'customs_short_and_import_duty' => $ratio * $totalCustomsShortAndImportDuty / 100,
                'other_expenses' => $ratio * $totalOtherExpenses / 100,
                'customs_collection' => $ratio * $totalCustomsCollection / 100,
                'transport_expenses' => $ratio * $totalTransportExpenses / 100,
                'import_fee_and_other_expenses' => $ratio * $totalImportFeeAndOtherExpenses / 100,
                'vat' => $ratio * $totalVat / 100,
                'subtotal_amount' => $importQueryDetail->subtotal_amount,
                'subtotal_amount_azn' => $subtotalAmountAZN,
                'price_per_unit_of_measure_azn' => $subtotalAmountAZN / $importQueryDetail->quantity,
            ];

            $totalAmount += $importQueryDetail->subtotal_amount;
        }

        $importCost = ImportCost::query()->create([
            'import_query_id' => $request->input('import_query_id'),
            'company_id' => $importQuery->company_id,
            'total_amount' => toFloat($totalAmount),
            'total_short_import_duty' => toFloat($totalShortImportDuty),
            'total_customs_short_and_import_duty' => toFloat($totalCustomsShortAndImportDuty),
            'total_other_expenses' => toFloat($totalOtherExpenses),
            'total_customs_collection' => toFloat($totalCustomsCollection),
            'total_transport_expenses' => toFloat($totalTransportExpenses),
            'total_import_fee_and_other_expenses' => toFloat($totalImportFeeAndOtherExpenses),
            'total_vat' => toFloat($totalVat),
            'total_amount_azn' => toFloat($totalAmountAZN),
        ]);

        foreach ($importCostDetails as $importCostDetail) {
            ImportCostDetail::query()->create([
                'import_cost_id' => $importCost->id,
                'import_query_detail_id' => $importCostDetail['import_query_detail_id'],
                'quantity' => toFloat($importCostDetail['quantity']),
                'ratio' => toFloat($importCostDetail['ratio']),
                'short_import_duty' => toFloat($importCostDetail['short_import_duty']),
                'customs_short_and_import_duty' => toFloat($importCostDetail['customs_short_and_import_duty']),
                'other_expenses' => toFloat($importCostDetail['other_expenses']),
                'customs_collection' => toFloat($importCostDetail['customs_collection']),
                'transport_expenses' => toFloat($importCostDetail['transport_expenses']),
                'import_fee_and_other_expenses' => toFloat($importCostDetail['import_fee_and_other_expenses']),
                'vat' => toFloat($importCostDetail['vat']),
                'subtotal_amount_azn' => toFloat($importCostDetail['subtotal_amount_azn']),
                'price_per_unit_of_measure_azn' => toFloat($importCostDetail['price_per_unit_of_measure_azn']),
            ]);
        }

        return $this->success(data: ImportCostResource::make($importCost),
            message: "İdxal sorğusu üzrə maya dəyəri əlavə edildi",
            code: 201);
    }

    public function show($importCost): JsonResponse
    {
        $importCost = ImportCost::query()->with(['importCostDetails'])->find($importCost);

        if (!$importCost) {
            return $this->error(message: "İdxal sorğusu üzrə maya dəyəri tapılmadı", code: 404);
        }

        return $this->success(data: ImportCostResource::make($importCost));
    }
}

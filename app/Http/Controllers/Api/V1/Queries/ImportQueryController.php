<?php

namespace App\Http\Controllers\Api\V1\Queries;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Queries\ImportQueryStoreRequest;
use App\Http\Requests\Api\V1\Queries\ImportQueryUpdateRequest;
use App\Http\Resources\Api\V1\Queries\ImportQueryCollection;
use App\Http\Resources\Api\V1\Queries\ImportQueryResource;
use App\Models\Currency;
use App\Models\Queries\ImportQuery;
use App\Models\Queries\ImportQueryDetail;
use App\Traits\HttpResponses;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ImportQueryController extends Controller
{
    use HttpResponses;

    public function index(Request $request): JsonResponse
    {
        $importQueries = ImportQuery::query()
            ->with(['company', 'currency'])
            ->paginate($request->input('limit') ?? 10);

        return $this->success(data: new ImportQueryCollection($importQueries));
    }

    public function store(ImportQueryStoreRequest $request): JsonResponse
    {
        $request->validated();

        $invoiceValue = 0;
        $customsTransactionFee = $request->input('customs_transaction_fee');
        $customsTransactionFee24 = $request->input('customs_transaction_fee_24_hours');
        $electronicCustomsFee = $request->input('electronic_customs_fee');
        $statisticValue = $request->input('statistic_value');
        $currencyId = $request->input('currency_id');
        $currency = Currency::query()->find($currencyId);
        $customsValue = $currency->rate * $statisticValue;
        $importFee = $customsValue * 0.15;
        $vat = ($customsValue + $customsTransactionFee + $customsTransactionFee24 + $importFee) * 0.18;
        $vatForElectronicCustomsFee = $electronicCustomsFee * 0.18;

        $importQueryDetails = [];

        foreach ($request->input('import_query_details') as $key => $importQueryDetail) {
            $importQueryDetails[$key]['material_title_local'] = $importQueryDetail['material_title_local'];
            $importQueryDetails[$key]['material_barcode'] = $importQueryDetail['material_barcode'];
            $importQueryDetails[$key]['material_title_az'] = $importQueryDetail['material_title_az'];
            $importQueryDetails[$key]['measure'] = $importQueryDetail['measure'];
            $importQueryDetails[$key]['quantity'] = $importQueryDetail['quantity'];
            $importQueryDetails[$key]['price_per_unit_of_measure'] = $importQueryDetail['price_per_unit_of_measure'];
            $subtotalAmount = $importQueryDetail['quantity'] * $importQueryDetail['price_per_unit_of_measure'];
            $importQueryDetails[$key]['subtotal_amount'] = $subtotalAmount;

            $invoiceValue += $subtotalAmount;
        }

        $importQuery = ImportQuery::query()->create([
            'company_id' => $request->input('company_id'),
            'query_number' => $request->input('query_number'),
            'customs_barcode' => $request->input('customs_barcode'),
            'seller_company_name' => $request->input('seller_company_name'),
            'currency_id' => $request->input('currency_id'),
            'shipping_from' => $request->input('shipping_from'),
            'delivery_date' => $request->input('delivery_date'),
            'customs_date' => $request->input('customs_date'),
            'transport_type' => $request->input('transport_type'),
            'payment_status' => $request->input('payment_status'),
            'net_weight' => toFloat($request->input('net_weight')),
            'statistic_value' => toFloat($request->input('statistic_value')), // ???
            'invoice_value' => toFloat($invoiceValue),
            'customs_value' => toFloat($customsValue),
            'customs_transaction_fee' => toFloat($customsTransactionFee), // $request->input('customs_transaction_fee')
            'customs_transaction_fee_24_hours'
            => toFloat($customsTransactionFee24), // $request->input('customs_transaction_fee_24_hours')
            'import_fee' => toFloat($importFee), // $request->input('import_fee'), // customsValue*0.15
            'vat' => toFloat($vat), // $request->input('vat')
            'electronic_customs_fee' => toFloat($electronicCustomsFee), // $request->input('electronic_customs_fee')
            'vat_for_electronic_customs_fee'
            => toFloat($vatForElectronicCustomsFee), // $request->input('vat_for_electronic_customs_fee')
        ]);

        foreach ($importQueryDetails as $importQueryDetail) {
            ImportQueryDetail::query()->create([
                'import_query_id' => $importQuery->id,
                'material_title_local' => $importQueryDetail['material_title_local'],
                'material_barcode' => $importQueryDetail['material_barcode'],
                'material_title_az' => $importQueryDetail['material_title_az'],
                'measure' => $importQueryDetail['measure'],
                'quantity' => toFloat($importQueryDetail['quantity']),
                'price_per_unit_of_measure' => toFloat($importQueryDetail['price_per_unit_of_measure']),
                'subtotal_amount' => toFloat($importQueryDetail['subtotal_amount']),
            ]);
        }

        return $this->success(data: ImportQueryResource::make($importQuery),
            message: "İdxal sorğusu uğurla əlavə olundu", code: 201);
    }

    public function update($importQuery, ImportQueryUpdateRequest $request): JsonResponse
    {
        $importQuery = ImportQuery::query()
            ->find($importQuery);

        if (!$importQuery) {
            return $this->error(message: "İdxal sorğusu tapılmadı", code: 404);
        }

        $invoiceValue = 0;
        $customsTransactionFee = toFloat($request->input('customs_transaction_fee'));
        $customsTransactionFee24 = toFloat($request->input('customs_transaction_fee_24_hours'));
        $electronicCustomsFee = toFloat($request->input('electronic_customs_fee'));
        $statisticValue = toFloat($request->input('statistic_value'));
        $currencyId = $request->input('currency_id');
        $currency = Currency::query()->find($currencyId);
        $customsValue = $currency->rate * $statisticValue;
        $importFee = toFloat($customsValue * 0.15);
        $vat = toFloat(($customsValue + $customsTransactionFee + $customsTransactionFee24 + $importFee) * 0.18);
        $vatForElectronicCustomsFee = toFloat($electronicCustomsFee * 0.18);

        $importQueryDetails = [];

        foreach ($request->input('import_query_details') as $key => $importQueryDetail) {
            $importQueryDetails[$key]['material_title_local'] = $importQueryDetail['material_title_local'];
            $importQueryDetails[$key]['material_barcode'] = $importQueryDetail['material_barcode'];
            $importQueryDetails[$key]['material_title_az'] = $importQueryDetail['material_title_az'];
            $importQueryDetails[$key]['measure'] = $importQueryDetail['measure'];
            $importQueryDetails[$key]['quantity'] = $importQueryDetail['quantity'];
            $importQueryDetails[$key]['price_per_unit_of_measure'] = $importQueryDetail['price_per_unit_of_measure'];
            $subtotalAmount = $importQueryDetail['quantity'] * $importQueryDetail['price_per_unit_of_measure'];
            $importQueryDetails[$key]['subtotal_amount'] = $subtotalAmount;
            $invoiceValue += $subtotalAmount;
        }

        $importQuery->update([
            'company_id' => $request->input('company_id'),
            'query_number' => $request->input('query_number'),
            'customs_barcode' => $request->input('customs_barcode'),
            'seller_company_name' => $request->input('seller_company_name'),
            'currency_id' => $request->input('currency_id'),
            'shipping_from' => $request->input('shipping_from'),
            'delivery_date' => $request->input('delivery_date'),
            'customs_date' => $request->input('customs_date'),
            'transport_type' => $request->input('transport_type'),
            'payment_status' => $request->input('payment_status'),
            'net_weight' => $request->input('net_weight'),
            'statistic_value' => toFloat($request->input('statistic_value')),
            'invoice_value' => toFloat($invoiceValue),
            'customs_value' => toFloat($customsValue),
            'customs_transaction_fee' => toFloat($customsTransactionFee), // $request->input('customs_transaction_fee')
            'customs_transaction_fee_24_hours'
            => toFloat($customsTransactionFee24), // $request->input('customs_transaction_fee_24_hours')
            'import_fee' => toFloat($importFee), // $request->input('import_fee'), // customsValue*0.15
            'vat' => toFloat($vat), // $request->input('vat')
            'electronic_customs_fee' => toFloat($electronicCustomsFee), // $request->input('electronic_customs_fee')
            'vat_for_electronic_customs_fee'
            => toFloat($vatForElectronicCustomsFee), // $request->input('vat_for_electronic_customs_fee')
        ]);

        ImportQueryDetail::query()->where('import_query_id', $importQuery->id)->delete();

        foreach ($importQueryDetails as $importQueryDetail) {
            ImportQueryDetail::query()->create(
                [
                    'import_query_id' => $importQuery->id,
                    'material_title_local' => $importQueryDetail['material_title_local'],
                    'material_barcode' => $importQueryDetail['material_barcode'],
                    'material_title_az' => $importQueryDetail['material_title_az'],
                    'measure' => toFloat($importQueryDetail['measure']),
                    'quantity' => toFloat($importQueryDetail['quantity']),
                    'price_per_unit_of_measure' => toFloat($importQueryDetail['price_per_unit_of_measure']),
                    'subtotal_amount'
                    => toFloat($importQueryDetail['quantity'] * $importQueryDetail['price_per_unit_of_measure']),
                ]);
        }

        return $this->success(data: ImportQueryResource::make($importQuery), message: "Idxal sorğusu uğurla yeniləndi");
    }


    public function show($importQuery): JsonResponse
    {
        $importQuery = ImportQuery::query()
            ->with(['importQueryDetails', 'company', 'currency'])
            ->find($importQuery);

        if (!$importQuery) {
            return $this->error(message: "İdxal sorğusu tapılmadı", code: 404);
        }

        return $this->success(data: ImportQueryResource::make($importQuery));
    }

    public function destroy($importQuery): JsonResponse
    {
        $importQuery = ImportQuery::query()
            ->find($importQuery);

        if (!$importQuery) {
            return $this->error(message: "İdxal sorğusu tapılmadı", code: 404);
        }

        $importQuery->delete();

        return $this->success(message: "İdxal sorğusu uğurla silindi");
    }
}

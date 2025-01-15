<?php

namespace App\Http\Controllers\Api\V1\Companies\Treasure;

use App\Enums\TransactionTypesEnum;
use App\Enums\TreasureTypesEnum;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\Companies\Treasure\TreasureTransactionCollection;
use App\Http\Resources\Api\V1\Companies\Treasure\TreasureTransactionResource;
use App\Models\Company\Treasure;
use App\Models\Company\TreasureTransaction;
use App\Models\Currency;
use App\Traits\HttpResponses;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TreasureTransactionController extends Controller
{
    use HttpResponses;

    public function index(Request $request): JsonResponse
    {
        $companyId = getHeaderCompanyId();

        if ($companyId) {
            $treasureTransactions = TreasureTransaction::query()
                ->where('company_id', $companyId)
                ->with([
                    'company:id,company_name,company_short_name'
                ])
                ->paginate($request->limit ?? 10);
        } else {
            return $this->error(message: "Şirkət tapılmadı", code: 404);
        }

        return $this->success(data: new TreasureTransactionCollection($treasureTransactions));
    }

    public function show($treasureTransaction): JsonResponse
    {
        $companyId = getHeaderCompanyId();

        if (!$companyId) {
            return $this->error(message: "Şirkət tapılmadı", code: 404);
        }

        $treasureTransaction = TreasureTransaction::query()
            ->where('company_id', $companyId)
            ->with([
                'company:id,company_name,company_short_name',
                'treasure:id,name,balance',
                'treasure_transaction:id,destination,invoice_number,transaction_date',
            ])
            ->find($treasureTransaction);

        if (!$treasureTransaction) {
            return $this->error(message: 'Kassa əməliyyatı tapılmadı', code: 404);
        }

        return $this->success(data: TreasureTransactionResource::make($treasureTransaction));
    }

    public function store(Request $request): JsonResponse
    {
        $companyId = getHeaderCompanyId();

        if (!$companyId) {
            return $this->error(message: "Şirkət tapılmadı", code: 404);
        }

        $validate = [
            'type_of_treasure' => ['required', Rule::in(TreasureTypesEnum::toArray())],
            'type_of_transaction' => ['required', Rule::in(TransactionTypesEnum::toArray())],
            'amount' => ['required', 'numeric', 'min:1'],
            'destination' => ['required', 'string', 'max:255'],
            'invoice_number' => ['required', 'string', 'max:255'],
            'note' => ['nullable', 'string'],
            'treasure_id' => ['required', 'exists:treasures,id', Rule::exists('treasures', 'id')
                ->where('company_id', $companyId)],
            'treasure_transaction_id' => ['nullable', 'integer',
                Rule::requiredIf($request->input('type_of_transaction') == 'REFUND'),
                Rule::exists('treasure_transactions', 'id')
                    ->where('company_id', $companyId)->where('type_of_transaction', 'INCOME')
                    ->whereNull('treasure_transaction_id')],
            'transaction_date' => ['required', 'date', 'date_format:Y-m-d'],
            'contract_date' => ['required', 'date', 'date_format:Y-m-d']
        ];

        $request->validate($validate);

        $incomeTransaction = null;

        $treasure = Treasure::query()->where('company_id', $companyId)
            ->find($request->input('treasure_id'));

        $amount = $request->input('amount');

        if ($request->input('type_of_treasure') == "CASH") {
            switch ($request->input('type_of_transaction')) {
                case "INCOME":
                    $treasure->increment('balance', $amount);
                    break;
                case "EXPENSE":
                    if ($treasure->balance > $amount) {
                        $treasure->decrement('balance', $amount);
                    } else {
                        return $this->error(message: 'Kassa hesabında yetərli məbləğ yoxdur', code: 400);
                    }
                    break;
                default :
                    $incomeTransaction = TreasureTransaction::query()->where('company_id', $companyId)
                        ->where('type_of_transaction', '=', 'INCOME')
                        ->where('is_disabled', '=', false)
                        ->findOrFail($request->input('treasure_transaction_id'));

                    if ($incomeTransaction->is_disabled) {
                        return $this->error(message: 'Kassa məxaric əməliyyatı tapılmadı', code: 400);
                    }

                    $incomeTransaction->update([
                        'is_disabled' => true
                    ]);
                    $treasure->increment('balance', $incomeTransaction->amount);
                    break;
            }
        } else {
            $validate['currency_id'] = ['required', 'exists:currencies,id'];
            $request->validate($validate);

            $currency = Currency::query()->findOrFail($request->input('currency_id'));
            $rate = $currency ? $currency->rate : 1;
            $amount = $amount * $rate;

            switch ($request->input('type_of_transaction')) {
                case "INCOME":
                    $treasure->increment('balance', $amount);
                    break;
                case "EXPENSE":
                    if ($treasure->balance > $amount) {
                        $treasure->decrement('balance', $amount);
                    } else {
                        return $this->error(message: 'Kassa hesabında yetərli məbləğ yoxdur', code: 404);
                    }
                    break;
                default :
                    $incomeTransaction = TreasureTransaction::query()->where('company_id', $companyId)
                        ->where('type_of_transaction', '=', 'INCOME')
                        ->where('is_disabled', '=', false)
                        ->findOrFail($request->input('treasure_transaction_id'));

                    if ($incomeTransaction->is_disabled) {
                        return $this->error(message: 'Kassa məxaric əməliyyatı tapılmadı', code: 404);
                    }

                    $incomeTransaction->update([
                        'is_disabled' => true
                    ]);

                    $treasure->increment('balance', $incomeTransaction->amount);
                    break;
            }
        }

        $treasure->update([
            'last_transaction_date' => $request->input('transaction_date'),
        ]);

        $treasure->refresh();

        $treasureTransaction = TreasureTransaction::query()->create([
            'company_id' => $companyId,
            'invoice_number' => $request->input('invoice_number'),
            'type_of_treasure' => $request->input('type_of_treasure'),
            'type_of_transaction' => $request->input('type_of_transaction'),
            'amount' => $request->input('type_of_transaction') == 'REFUND' ?
                $incomeTransaction->amount : $request->input('amount'),
            'contract_date' => $request->input('contract_date'),
            'currency_id' => $request->input('currency_id') ?? null,
            'destination' => $request->input('destination'),
            'note' => $request->input('note'),
            'treasure_id' => $request->input('treasure_id'),
            'treasure_transaction_id' => $request->input('treasure_transaction_id'),
            'transaction_date' => $request->input('transaction_date'),
            'treasure_balance' => $treasure->balance
        ]);

        return $this->success(data: TreasureTransactionResource::make($treasureTransaction),
            message: "Kassa əməliyyatı uğurla yaradıldı", code: 201);
    }

    public function destroy($treasureTransaction): JsonResponse
    {
        $companyId = getHeaderCompanyId();

        if (!$companyId) {
            return $this->error(message: "Şirkət tapılmadı", code: 404);
        }

        $treasureTransaction = TreasureTransaction::query()
            ->where('company_id', $companyId)
            ->with(['treasure', 'treasure_transaction'])
            ->findOrFail($treasureTransaction);

        switch ($treasureTransaction->type_of_transaction) {
            case "INCOME":
                $treasureTransaction->treasure->decrement('balance', $treasureTransaction->amount);
                break;
            case "EXPENSE":
                $treasureTransaction->treasure->increment('balance', $treasureTransaction->amount);
                break;
            default :
                $treasureTransaction->treasure_transaction->update([
                    'is_disabled' => false
                ]);
                $treasureTransaction->treasure
                    ->increment('balance', $treasureTransaction->treasure_transaction->amount);
                break;
        }

        $treasureTransaction->delete();

        return $this->success(message: 'Kassa silindi');
    }
}

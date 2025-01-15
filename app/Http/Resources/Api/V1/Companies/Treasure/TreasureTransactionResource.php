<?php

namespace App\Http\Resources\Api\V1\Companies\Treasure;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TreasureTransactionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $typeOfTreasures = [
            [
                'value' => 'CASH',
                'label' => trans('treasure_types.CASH')
            ],
            [
                'value' => 'TRANSFER',
                'label' => trans('treasure_types.TRANSFER')
            ],
            [
                'value' => 'VAT_DEPOSIT',
                'label' => trans('treasure_types.VAT_DEPOSIT')
            ]
        ];
        $typeOfTransactions = [
            [
                'value' => 'INCOME',
                'label' => trans('transaction_types.INCOME')
            ],
            [
                'value' => 'EXPENSE',
                'label' => trans('transaction_types.EXPENSE')
            ],
            [
                'value' => 'REFUND',
                'label' => trans('transaction_types.REFUND')
            ]
        ];

        return [
            'id' => $this->id,
            'type_of_treasure' => getLabelValue($this->type_of_treasure, $typeOfTreasures),
            'type_of_transaction' => getLabelValue($this->type_of_transaction, $typeOfTransactions),
            'invoice_number' => $this->invoice_number,
            'note' => $this->note,
            'amount' => $this->amount,
            'contract_date' => $this->contract_date,
            'transaction_date' => $this->transaction_date,
            'destination' => $this->destination,
            'currency_id' => $this->currency_id,
            'currency' => $this->whenLoaded('currency'),
            'company_id' => $this->company_id,
            'company' => $this->whenLoaded('company'),
            'treasure_id' => $this->treasure_id,
            'treasure' => $this->whenLoaded('treasure'),
            'treasure_balance' => $this->treasure_balance,
            'treasure_transaction_id' => $this->treasure_transaction_id,
            'treasure_transaction' => $this->whenLoaded('treasure_transaction'),
            'is_disabled' => $this->is_disabled
        ];
    }
}

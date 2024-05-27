<?php

namespace App\Http\Resources\Api\V1\Queries\ImportCost;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ImportCostDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'import_cost_id' => $this->import_cost_id,
            'import_query_detail_id' => $this->import_query_detail_id,
            'ratio' => $this->ratio,
            'short_import_duty' => $this->short_import_duty,
            'customs_short_and_import_duty' => $this->customs_short_and_import_duty,
            'other_expenses' => $this->other_expenses,
            'customs_collection' => $this->customs_collection,
            'transport_expenses' => $this->transport_expenses,
            'import_fee_and_other_expenses' => $this->import_fee_and_other_expenses,
            'vat' => $this->vat,
            'subtotal_amount_azn' => $this->subtotal_amount_azn,
            'price_per_unit_of_measure_azn' => $this->price_per_unit_of_measure_azn,
            'quantity' => $this->quantity,
        ];
    }
}

<?php

namespace App\Http\Resources\Api\V1\Queries\ImportCost;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ImportCostResource extends JsonResource
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
            'import_query_id' => $this->import_query_id,
            'total_ratio' => $this->total_ratio,
            'total_amount' => $this->total_amount,
            'total_short_import_duty' => $this->total_short_import_duty,
            'total_customs_short_and_import_duty' => $this->total_customs_short_and_import_duty,
            'total_other_expenses' => $this->total_other_expenses,
            'total_customs_collection' => $this->total_customs_collection,
            'total_transport_expenses' => $this->total_transport_expenses,
            'total_import_fee_and_other_expenses' => $this->total_import_fee_and_other_expenses,
            'total_vat' => $this->total_vat,
            'total_amount_azn' => $this->total_amount_azn,
            'importCostDetails' => ImportCostDetailResource::collection($this->whenLoaded('importCostDetails'))
        ];
    }
}

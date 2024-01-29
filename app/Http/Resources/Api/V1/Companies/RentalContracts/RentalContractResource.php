<?php

namespace App\Http\Resources\Api\V1\Companies\RentalContracts;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RentalContractResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => (int)$this->id,
            'company_id' => (int)$this->company_id,
            'type' => $this->type,
            'company' => $this->whenLoaded('company'),
            'object_name' => $this->object_name,
            'object_code' => $this->object_code,
            'creator_id' => (int)$this->creator_id,
            'creator' => $this->whenLoaded('creator'),
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'rental_area' => $this->rental_area,
            'rental_price' => $this->rental_price,
            'rental_price_with_vat' => $this->rental_price_with_vat,
            'is_vat' => $this->is_vat,
            'tenant_type' => $this->tenant_type,
            'contract_files' => $this->contract_files,
            'address' => $this->address,
            'created_at' => $this->created_at,
        ];
    }
}

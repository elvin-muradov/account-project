<?php

namespace App\Http\Resources\Api\V1\Companies\Treasure;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TreasureResource extends JsonResource
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
            'name' => $this->name,
            'company_id' => $this->company_id,
            'company' => $this->whenLoaded('company'),
            'balance' => $this->balance,
            'last_transaction_date' => $this->last_transaction_date,
        ];
    }
}

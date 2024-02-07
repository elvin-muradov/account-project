<?php

namespace App\Http\Resources\Api\V1\Companies\Warehouses;

use App\Http\Resources\Api\V1\Companies\Materials\MaterialCollection;
use App\Http\Resources\Api\V1\Companies\Materials\MaterialResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WarehouseResource extends JsonResource
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
            'company' => $this->whenLoaded('company') ?
                $this->company->only('id', 'company_name') : null,
            'materials' => $this->whenLoaded('materials') ?
                MaterialResource::collection($this->materials) : null,
            'created_at' => $this->created_at
        ];
    }
}

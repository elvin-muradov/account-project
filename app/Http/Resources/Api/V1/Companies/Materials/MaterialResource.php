<?php

namespace App\Http\Resources\Api\V1\Companies\Materials;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MaterialResource extends JsonResource
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
            'code' => $this->code,
            'description' => $this->description,
            'company_id' => $this->company_id,
            'warehouse_id' => $this->warehouse_id,
            'material_group_id' => $this->material_group_id,
            'company' => $this->whenLoaded('company') ?
                $this->company->only('id', 'company_name') : null,
            'warehouse' => $this->whenLoaded('warehouse') ?
                $this->warehouse?->only('id', 'name') : null,
            'materialGroup' => $this->whenLoaded('materialGroup') ?
                $this->materialGroup->only('id', 'name') : null,
            'created_at' => $this->created_at
        ];
    }
}

<?php

namespace App\Http\Resources\Api\V1\Companies\FixedAssets;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FixedAssetResource extends JsonResource
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
            'initial_value' => $this->initial_value,
            'useful_life_years' => $this->useful_life_years,
            'percentage' => $this->percentage,
            'type' => $this->type,
            'start_of_using' => $this->start_of_using,
            'end_of_using' => $this->end_of_using,
            'fixed_asset_category_id' => $this->fixed_asset_category_id,
            'fixedAssetCategory' => $this->whenLoaded('fixedAssetCategory'),
            'company_id' => $this->company_id,
            'company' => $this->whenLoaded('company'),
        ];
    }
}

<?php

namespace App\Http\Resources\Api\V1\Companies\FixedAssetCategories;

use App\Http\Resources\Api\V1\Companies\FixedAssets\FixedAssetCollection;
use App\Http\Resources\Api\V1\Companies\FixedAssets\FixedAssetResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FixedAssetCategoryResource extends JsonResource
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
            'fixedAssets' => $this->whenLoaded('fixedAssets',
                fn () => FixedAssetResource::collection($this->fixedAssets)),
        ];
    }
}

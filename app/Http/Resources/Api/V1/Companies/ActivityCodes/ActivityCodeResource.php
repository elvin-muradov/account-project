<?php

namespace App\Http\Resources\Api\V1\Companies\ActivityCodes;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ActivityCodeResource extends JsonResource
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
            'activity_code' => $this->activity_code,
            'company_id' => $this->company_id,
            'company' => [
                'id' => $this->company->id,
                'company_name' => $this->company->company_name
            ],
            'created_at' => $this->created_at,
        ];
    }
}

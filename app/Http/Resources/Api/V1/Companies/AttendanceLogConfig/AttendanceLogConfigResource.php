<?php

namespace App\Http\Resources\Api\V1\Companies\AttendanceLogConfig;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AttendanceLogConfigResource extends JsonResource
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
            'company_id' => $this->company_id,
            'company' => $this->whenLoaded('company'),
            'year' => $this->year,
            'month' => $this->month,
            'log_date' => $this->log_date,
            'config' => $this->config,
            'created_at' => $this->created_at,
        ];
    }
}

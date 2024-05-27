<?php

namespace App\Http\Resources\Api\V1\Companies\AttendanceLog;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AttendanceLogResource extends JsonResource
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
            'employee_id' => $this->employee_id,
            'company' => $this->whenLoaded('company'),
            'employee' => $this->whenLoaded('employee'),
            'year' => $this->year,
            'month' => $this->month,
            'days' => $this->days,
            'month_work_days' => $this->month_work_days,
            'celebration_days' => $this->celebration_days,
            'month_work_day_hours' => $this->month_work_day_hours,
            'created_at' => $this->created_at,
        ];
    }
}

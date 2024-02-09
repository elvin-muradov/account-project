<?php

namespace App\Http\Resources\Api\V1\Orders\MotherhoodHolidayOrders;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MotherhoodHolidayOrderResource extends JsonResource
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
            'order_number' => $this->order_number,
            'company_id' => $this->company_id,
            'company_name' => $this->company_name,
            'tax_id_number' => $this->tax_id_number,
            'name' => $this->name,
            'surname' => $this->surname,
            'father_name' => $this->father_name,
            'gender' => $this->gender,
            'position' => $this->position,
            'holiday_start_date' => $this->holiday_start_date,
            'holiday_end_date' => $this->holiday_end_date,
            'employment_start_date' => $this->employment_start_date,
            'type_of_holiday' => $this->type_of_holiday,
            'main_part_of_order' => $this->main_part_of_order,
            'generated_file' => $this->generated_file,
            'd_name' => $this->d_name,
            'd_surname' => $this->d_surname,
            'd_father_name' => $this->d_father_name,
            'created_at' => $this->created_at
        ];
    }
}

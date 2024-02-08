<?php

namespace App\Http\Resources\Api\V1\Orders\DefaultHolidayOrders;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DefaultHolidayOrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $genders = [
            [
                'value' => 'MALE',
                'label' => trans('genders.MALE')
            ],
            [
                'value' => 'FEMALE',
                'label' => trans('genders.FEMALE')
            ]
        ];

        return [
            'id' => $this->id,
            'order_number' => $this->order_number,
            'company_id' => $this->company_id,
            'company_name' => $this->company_name,
            'tax_id_number' => $this->tax_id_number,
            'main_part_of_order' => $this->main_part_of_order,
            'name' => $this->name,
            'surname' => $this->surname,
            'father_name' => $this->father_name,
            'position' => $this->position,
            'gender' => getLabelValue($this->gender, $genders),
            'holiday_start_date' => $this->holiday_start_date,
            'days_count' => $this->days_count,
            'holiday_end_date' => $this->holiday_end_date,
            'employment_start_date' => $this->employment_start_date,
            'd_name' => $this->d_name,
            'd_surname' => $this->d_surname,
            'd_father_name' => $this->d_father_name,
            'generated_file' => $this->generated_file,
            'created_at' => $this->created_at,
        ];
    }
}

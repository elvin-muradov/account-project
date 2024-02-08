<?php

namespace App\Http\Resources\Api\V1\Orders\TerminationOrders;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TerminationOrderResource extends JsonResource
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
            'company_name' => $this->company_name,
            'tax_id_number' => $this->tax_id_number,
            'main_part_of_order' => $this->main_part_of_order,
            'days_count' => $this->days_count,
            'name' => $this->name,
            'surname' => $this->surname,
            'gender' => getLabelValue($this->gender, $genders),
            'employment_start_date' => $this->employment_start_date,
            'termination_date' => $this->termination_date,
            'generated_file' => $this->generated_file,
            'd_name' => $this->d_name,
            'd_surname' => $this->d_surname,
            'd_father_name' => $this->d_father_name,
            'created_at' => $this->created_at
        ];
    }
}

<?php

namespace App\Http\Resources\Api\V1\Orders\BusinessTripOrders;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BusinessTripOrderResource extends JsonResource
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
            'company' => $this->whenLoaded('company') ?
                $this->company->only(['id', 'company_name']) : null,
            'company_name' => $this->company_name,
            'tax_id_number' => $this->tax_id_number,
            'first_part_of_order' => $this->first_part_of_order,
            'business_trip_to' => $this->business_trip_to,
            'name' => $this->name,
            'surname' => $this->surname,
            'father_name' => $this->father_name,
            'gender' => $this->gender,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'city_name' => $this->city_name,
            'order_date' => $this->order_date,
            'generated_file' => $this->generated_file,
            'd_name' => $this->d_name,
            'd_surname' => $this->d_surname,
            'd_father_name' => $this->d_father_name,
            'created_at' => $this->created_at,
        ];
    }
}

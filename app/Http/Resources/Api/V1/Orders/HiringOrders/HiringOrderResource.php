<?php

namespace App\Http\Resources\Api\V1\Orders\HiringOrders;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HiringOrderResource extends JsonResource
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
            'start_date' => $this->start_date,
            'position' => $this->position,
            'salary' => $this->salary,
            'salary_in_words' => $this->salary_in_words,
            'generated_file' => $this->generated_file,
            'd_name' => $this->d_name,
            'd_surname' => $this->d_surname,
            'd_father_name' => $this->d_father_name,
            'created_at' => $this->created_at,
        ];
    }
}

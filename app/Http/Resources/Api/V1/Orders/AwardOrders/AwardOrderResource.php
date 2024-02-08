<?php

namespace App\Http\Resources\Api\V1\Orders\AwardOrders;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AwardOrderResource extends JsonResource
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
            'main_part_of_order' => $this->main_part_of_order,
            'order_date' => $this->order_date,
            'd_name' => $this->d_name,
            'd_surname' => $this->d_surname,
            'd_father_name' => $this->d_father_name,
            'generated_file' => $this->generated_file,
            'worker_infos' => $this->worker_infos,
            'created_at' => $this->created_at
        ];
    }
}

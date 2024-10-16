<?php

namespace App\Http\Resources\Api\V1\Tasks;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskResource extends JsonResource
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
            'type' => $this->type,
            'subtype' => $this->subtype,
            'company_id' => $this->company_id,
            'company' => $this->company->only('id', 'company_name', 'asan_sign_expired_at', 'ydm_card_expired_at'),
            'employee_id' => $this->employee_id,
            'employee' => $this->employee?->only('id', 'name', 'surname', 'email', 'salary_card_expired_at'),
            'accountant_id' => $this->accountant_id,
            'accountant' => $this->accountant->only('id', 'name', 'surname', 'email'),
            'title' => $this->title,
            'description' => $this->description,
            'is_completed' => $this->is_completed,
            'completed_at' => $this->completed_at,
            'created_at' => $this->created_at->format('d.m.Y H:i'),
        ];
    }
}

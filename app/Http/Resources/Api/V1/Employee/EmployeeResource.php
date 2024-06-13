<?php

namespace App\Http\Resources\Api\V1\Employee;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EmployeeResource extends JsonResource
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
            'name' => $this->name,
            'surname' => $this->surname,
            'father_name' => $this->father_name,
            'company_id' => $this->company_id,
            'company' => $this->whenLoaded('company') ?
                $this->company->only('id', 'company_name') : null,
            'position_id' => $this->position_id,
            'position' => $this->whenLoaded('position') ?
                $this->position->only('id', 'name') : null,
            'employee_type' => $this->employee_type,
            'gender' => $this->gender,
            'birth_date' => $this->birth_date,
            'id_card_serial' => $this->id_card_serial,
            'fin_code' => $this->fin_code,
            'id_card_date' => $this->id_card_date,
            'ssn' => $this->ssn,
            'start_date_of_employment' => $this->start_date_of_employment,
            'end_date_of_employment' => $this->end_date_of_employment,
            'previous_job' => $this->previous_job,
            'phone' => $this->phone,
            'email' => $this->email,
            'work_experience' => $this->work_experience,
            'education' => $this->education,
            'salary' => $this->salary,
            'salary_card_expiration_date' => $this->salary_card_expiration_date,
            'created_at' => Carbon::parse($this->created_at)->format('d.m.Y'),
        ];
    }
}

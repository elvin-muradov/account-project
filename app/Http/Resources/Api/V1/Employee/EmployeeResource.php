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
            'company' => $this->whenLoaded('company'),
            'birth_date' => Carbon::parse($this->birth_date)->format('d.m.Y'),
            'id_card_serial' => $this->id_card_serial,
            'fin_code' => $this->fin_code,
            'id_card_date' => Carbon::parse($this->id_card_date)->format('d.m.Y'),
            'ssn' => $this->ssn,
            'start_date_of_employment' => Carbon::parse($this->start_date_of_employment)->format('d.m.Y'),
            'end_date_of_employment' => Carbon::parse($this->end_date_of_employment)->format('d.m.Y'),
            'previous_job' => $this->previous_job,
            'phone' => $this->phone,
            'email' => $this->email,
            'work_experience' => $this->work_experience,
            'education' => $this->education,
            'salary' => $this->salary,
            'salary_card_expiration_date' => Carbon::parse($this->salary_card_expiration_date)->format('d.m.Y'),
            'created_at' => Carbon::parse($this->created_at)->format('d.m.Y'),
        ];
    }
}

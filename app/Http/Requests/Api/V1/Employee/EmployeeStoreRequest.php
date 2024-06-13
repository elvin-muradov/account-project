<?php

namespace App\Http\Requests\Api\V1\Employee;

use App\Enums\EducationTypesEnum;
use App\Enums\EmployeeTypes;
use App\Enums\GenderTypes;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EmployeeStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'surname' => ['required', 'string', 'max:255'],
            'father_name' => ['nullable', 'string', 'max:255'],
            'birth_date' => ['required', 'date'],
            'id_card_serial' => ['required', 'string', 'min:9', 'max:12', 'unique:employees,id_card_serial'],
            'fin_code' => ['required', 'min:7', 'max:7', 'string',
                'unique:employees,fin_code', 'regex:/^[a-zA-Z0-9]{7}$/'],
            'id_card_date' => ['required', 'date'],
            'ssn' => ['required', 'numeric', 'digits:13', 'unique:employees,ssn'],
            'start_date_of_employment' => ['required', 'date'],
            'gender' => ['required', 'in:' . GenderTypes::toString()],
            'end_date_of_employment' => ['nullable', 'date'],
            'previous_job' => ['nullable', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:255', 'unique:employees,phone', 'phone:AZ'],
            'email' => ['required', 'string', 'email:rfc,dns', 'max:255', 'unique:employees,email'],
            'work_experience' => ['nullable', 'numeric'],
            'education' => ['required', 'in:' . EducationTypesEnum::toString()],
            'salary' => ['nullable', 'numeric'],
            'salary_card_expiration_date' => ['nullable', 'date'],
            'employee_type' => ['required', 'in:' . EmployeeTypes::toString()],
            'password' => ['nullable',
                Rule::requiredIf($this
                        ->employee_type == EmployeeTypes::DIRECTOR->value ||
                    $this->employee_type == EmployeeTypes::FOUNDER->value),
                'confirmed', 'string', 'min:8', 'max:16'],
            'company_id' => ['required', 'numeric', 'exists:companies,id'],
            'position_id' => ['required', 'integer',
                Rule::exists("positions", "id")
                    ->where("company_id", $this->company_id)],
        ];
    }
}

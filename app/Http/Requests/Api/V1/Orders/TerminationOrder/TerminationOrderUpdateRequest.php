<?php

namespace App\Http\Requests\Api\V1\Orders\TerminationOrder;

use App\Enums\GenderTypes;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TerminationOrderUpdateRequest extends FormRequest
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
            'company_id' => ['required', 'exists:companies,id'],
            'employee_id' => ['required', 'integer', Rule::exists('employees', 'id')
                ->where('company_id', $this->input('company_id'))
            ],
            'employment_start_date' => ['required', 'date'],
            'termination_date' => ['required', 'date'],
            'days_count' => ['required', 'integer'],
            'main_part_of_order' => ['required', 'string'],
        ];
    }
}

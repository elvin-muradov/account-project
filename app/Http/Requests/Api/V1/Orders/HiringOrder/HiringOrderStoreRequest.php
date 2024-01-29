<?php

namespace App\Http\Requests\Api\V1\Orders\HiringOrder;

use App\Enums\GenderTypes;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class HiringOrderStoreRequest extends FormRequest
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
            'order_number' => ['required', 'string', 'max:255', 'unique:hiring_orders,order_number'],
            'company_id' => ['required', 'exists:companies,id'],
            'tax_id_number' => ['required', 'integer', 'digits:10'],
            'name' => ['required', 'string', 'max:255'],
            'surname' => ['required', 'string', 'max:255'],
            'father_name' => ['required', 'string', 'max:255'],
            'gender' => ['required', 'in:' . GenderTypes::toString()],
            'start_date' => ['required', 'date'],
            'position' => ['required', 'string', 'max:255'],
            'salary' => ['required', 'numeric'],
            'salary_in_words' => ['required', 'string', 'max:255'],
            'd_name' => ['required', 'string', 'max:255'],
            'd_surname' => ['required', 'string', 'max:255'],
            'd_father_name' => ['required', 'string', 'max:255'],
        ];
    }
}

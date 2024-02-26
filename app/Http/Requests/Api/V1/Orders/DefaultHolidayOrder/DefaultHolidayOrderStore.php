<?php

namespace App\Http\Requests\Api\V1\Orders\DefaultHolidayOrder;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class DefaultHolidayOrderStore extends FormRequest
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
            'order_number' => ['nullable', 'string', 'max:255',
                'unique:default_holiday_orders,order_number'],
            'company_id' => ['required', 'exists:companies,id'],
            'tax_id_number' => ['required', 'integer', 'digits:10'],
            'name' => ['required', 'string', 'max:255'],
            'surname' => ['required', 'string', 'max:255'],
            'father_name' => ['required', 'string', 'max:255'],
            'position' => ['required', 'string', 'max:255'],
            'days_count' => ['required', 'integer', 'min:1'],
            'holiday_start_date' => ['required', 'date'],
            'holiday_end_date' => ['required', 'date'],
            'employment_start_date' => ['required', 'date'],
            'main_part_of_order' => ['required'],
            'd_name' => ['required', 'string', 'max:255'],
            'd_surname' => ['required', 'string', 'max:255'],
            'd_father_name' => ['required', 'string', 'max:255'],
        ];
    }
}

<?php

namespace App\Http\Requests\Api\V1\Orders\BusinessTripOrder;

use App\Enums\GenderTypes;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class BusinessTripOrderUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'order_number' => ['required', 'string', 'max:255',
                'unique:business_trip_orders,order_number,' . $this->businessTripOrder],
            'company_id' => ['required', 'exists:companies,id'],
            'tax_id_number' => ['required', 'integer', 'digits:10'],
            'name' => ['required', 'string', 'max:255'],
            'surname' => ['required', 'string', 'max:255'],
            'father_name' => ['required', 'string', 'max:255'],
            'gender' => ['required', 'in:' . GenderTypes::toString()],
            'business_trip_to' => ['required', 'string'],
            'first_part_of_order' => ['required', 'string'],
            'position' => ['required', 'string', 'max:255'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date'],
            'city_name' => ['required', 'string', 'max:255'],
            'order_date' => ['required', 'date'],
            'd_name' => ['required', 'string', 'max:255'],
            'd_surname' => ['required', 'string', 'max:255'],
            'd_father_name' => ['required', 'string', 'max:255'],
        ];
    }
}

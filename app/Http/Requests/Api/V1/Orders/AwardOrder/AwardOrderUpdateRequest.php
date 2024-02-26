<?php

namespace App\Http\Requests\Api\V1\Orders\AwardOrder;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class AwardOrderUpdateRequest extends FormRequest
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
                'unique:award_orders,order_number,' . $this->awardOrder],
            'company_id' => ['required', 'exists:companies,id'],
            'tax_id_number' => ['required', 'integer', 'digits:10'],
            'order_date' => ['required', 'date'],
            'main_part_of_order' => ['required'],
            'd_name' => ['required', 'string', 'max:255'],
            'd_surname' => ['required', 'string', 'max:255'],
            'd_father_name' => ['required', 'string', 'max:255'],
            'worker_infos' => ['required', 'array'],
            'worker_infos.*.position' => ['required', 'string', 'max:255'],
            'worker_infos.*.salary' => ['required', 'string', 'max:255'],
        ];
    }
}

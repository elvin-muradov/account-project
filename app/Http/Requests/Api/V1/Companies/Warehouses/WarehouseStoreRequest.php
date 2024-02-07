<?php

namespace App\Http\Requests\Api\V1\Companies\Warehouses;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class WarehouseStoreRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255', Rule::unique('warehouses', 'name')
                ->where('company_id', $this->company_id)],
            'company_id' => ['required', 'integer', 'exists:companies,id'],
        ];
    }
}

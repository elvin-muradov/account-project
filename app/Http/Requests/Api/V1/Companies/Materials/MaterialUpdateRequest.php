<?php

namespace App\Http\Requests\Api\V1\Companies\Materials;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MaterialUpdateRequest extends FormRequest
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
            'code' => ['nullable', 'string', 'max:255', Rule::unique('materials', 'code')
                ->where('company_id', $this->company_id)->ignore($this->material)],
            'description' => ['nullable', 'string', 'max:255'],
            'company_id' => ['required', 'integer', 'exists:companies,id'],
            'material_group_id' => ['required', 'integer',
                Rule::exists('material_groups', 'id')
                    ->where('company_id', $this->company_id)],
            'warehouse_id' => ['nullable', 'integer', 'exists:warehouses,id',
                Rule::exists('warehouses', 'id')
                    ->where('company_id', $this->company_id)],
        ];
    }
}

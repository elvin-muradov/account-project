<?php

namespace App\Http\Requests\Api\V1\Queries\ImportCost;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ImportCostStoreRequest extends FormRequest
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
            'import_query_id' => ['required', 'integer', 'exists:import_queries,id'],
            'total_ratio' => ['nullable', 'numeric'],
            'total_amount' => ['nullable', 'numeric'],
            'total_short_import_duty' => ['nullable', 'numeric'],
            'total_customs_short_and_import_duty' => ['nullable', 'numeric'],
            'total_other_expenses' => ['nullable', 'numeric'],
            'total_customs_collection' => ['nullable', 'numeric'],
            'total_transport_expenses' => ['nullable', 'numeric'],
            'total_import_fee_and_other_expenses' => ['nullable', 'numeric'],
            'total_vat' => ['nullable', 'numeric'],
            'total_amount_azn' => ['nullable', 'numeric'],
        ];
    }
}

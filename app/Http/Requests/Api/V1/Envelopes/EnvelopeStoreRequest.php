<?php

namespace App\Http\Requests\Api\V1\Envelopes;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class EnvelopeStoreRequest extends FormRequest
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
            'from_company_id' => ['required', 'exists:companies,id'],
            'to_company_id' => ['required', 'exists:companies,id'],
            'envelopes' => ['required', 'array'],
            'envelopes.*' => ['required', 'file', 'mimes:png,jpg,jpeg,pdf,xlsx,xls,docx,doc'],
        ];
    }
}

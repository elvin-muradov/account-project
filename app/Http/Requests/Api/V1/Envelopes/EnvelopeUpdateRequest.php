<?php

namespace App\Http\Requests\Api\V1\Envelopes;

use App\Enums\EnvelopeTypes;
use App\Models\Envelopes\Envelope;
use Carbon\Carbon;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EnvelopeUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function prepareForValidation(): void
    {
        if ($this->type === EnvelopeTypes::INCOMING->value) {
            $this->merge([
                'from_company_id' => null,
                'to_company_name' => null
            ]);
        }

        if ($this->type === EnvelopeTypes::OUTGOING->value) {
            $this->merge([
                'to_company_id' => null,
                'from_company_name' => null
            ]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        $envelope = Envelope::query()->find($this->envelope);

        return [
            'type' => ['required', 'in:' . EnvelopeTypes::toString()],
            'from_company_id' => ['nullable',
                Rule::requiredIf($this->type === EnvelopeTypes::OUTGOING->value),
                'exists:companies,id'],
            'to_company_id' => ['nullable',
                Rule::requiredIf($this->type === EnvelopeTypes::INCOMING->value),
                'exists:companies,id'],
            'from_company_name' => ['nullable',
                Rule::requiredIf($this->type === EnvelopeTypes::INCOMING->value),
                'string', 'max:255'],
            'to_company_name' => ['nullable',
                Rule::requiredIf($this->type === EnvelopeTypes::OUTGOING->value),
                'string', 'max:255'],
            'envelopes' => ['nullable', 'array'],
            'envelopes.*' => ['required', 'file', 'mimes:png,jpg,jpeg,pdf,xlsx,xls,docx,doc'],
        ];
    }
}

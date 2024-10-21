<?php

namespace App\Http\Requests\Api\V1\Companies;

use App\Enums\CompanyCategoriesEnum;
use App\Enums\CompanyObligationsEnum;
use App\Enums\UserTypesEnum;
use App\Models\Company\Company;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CompanyUpdateRequest extends FormRequest
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
        $usedCompany = Company::query()->find($this->company);

        return [
            'company_name' => ['required', 'string', 'min:3', 'max:35',
                'unique:companies,company_name,' . $this->company],
            'company_short_name' => ['required', 'string', 'min:2', 'max:6'],
            'company_category' => ['required', 'in:' . CompanyCategoriesEnum::toString()],
            'company_obligation' => ['required', 'in:' . CompanyObligationsEnum::toString()],
            'company_address' => ['nullable', 'string', 'min:3', 'max:255'],
            'company_emails' => ['required', 'array'],
            'company_emails.*' => ['required', 'email:rfc,dns'],
            'owner_type' => ['nullable', 'in:' . UserTypesEnum::toString()],
            'tax_id_number' => ['required', 'integer', 'digits:10'],
            'tax_id_number_date' => ['required', 'date'],
            'dsmf_number' => ['required', 'integer', 'digits:13'],
            'main_user_id' => ['nullable', 'integer', 'exists:users,id'],
            'director_id' => ['nullable', 'integer', 'exists:users,id'],
            'tax_id_number_files' => ['nullable', 'array', Rule::requiredIf(empty($usedCompany->tax_id_number_files))],
            'tax_id_number_files.*' => ['required', 'file', 'mimes:png,jpg,jpeg,pdf,xlsx,xls,docx,doc'],
            'charter_files' => ['nullable', 'array', Rule::requiredIf(empty($usedCompany->charter_files))],
            'charter_files.*' => ['required', 'file', 'mimes:png,jpg,jpeg,pdf,xlsx,xls,docx,doc'],
            'extract_files' => ['nullable', 'array', Rule::requiredIf(empty($usedCompany->extract_files))],
            'extract_files .*' => ['required', 'file', 'mimes:png,jpg,jpeg,pdf,xlsx,xls,docx,doc'],
            'director_id_card_files' => ['nullable', 'array',
                Rule::requiredIf(empty($usedCompany->director_id_card_files))],
            'director_id_card_files .*' => ['required', 'file', 'mimes:png,jpg,jpeg,pdf,xlsx,xls,docx,doc'],
            'creators_files' => ['nullable', 'array', Rule::requiredIf(empty($usedCompany->creators_files))],
            'creators_files .*' => ['mimes:png,jpg,jpeg,pdf,xlsx,xls,docx,doc'],
            'fixed_asset_files_exists' => ['required', 'boolean'],
            'fixed_asset_files' => ['nullable', 'array', Rule::requiredIf(empty($usedCompany->fixed_asset_files))],
            'fixed_asset_files .*' => ['nullable', 'file', 'mimes:png,jpg,jpeg,pdf,xlsx,xls,docx,doc'],
            'founding_decision_files' => ['nullable', 'array',
                Rule::requiredIf(empty($usedCompany->founding_decision_files))],
            'founding_decision_files .*' => ['required', 'file', 'mimes:png,jpg,jpeg,pdf,xlsx,xls,docx,doc'],
            'asan_sign' => ['required', 'phone:AZ'],
            'asan_sign_start_date' => ['required', 'date'],
            'asan_sign_expired_at' => ['required', 'date'],
            'asan_id' => ['required', 'string'],
            'pin1' => ['required', 'integer', 'digits:4'],
            'pin2' => ['required', 'integer', 'digits:5'],
            'puk' => ['required', 'integer', 'digits:8'],
            'statistic_code' => ['required', 'integer', 'digits:7'],
            'statistic_password' => ['required', 'string', 'max:255'],
            'operator_azercell_account' => ['required', 'email:rfc,dns'],
            'operator_azercell_password' => ['required', 'max:255'],
            'ydm_account_email' => ['nullable', 'email:rfc,dns'],
            'ydm_password' => ['nullable', 'max:255'],
            'ydm_card_expired_at' => ['nullable', 'date'],
            'delete_tax_id_number_files' => ['sometimes', 'array'],
            'delete_charter_files' => ['sometimes', 'array'],
            'delete_extract_files' => ['sometimes', 'array'],
            'delete_director_id_card_files' => ['sometimes', 'array'],
            'delete_creators_files' => ['sometimes', 'array'],
            'delete_fixed_asset_files' => ['sometimes', 'array'],
            'delete_founding_decision_files' => ['sometimes', 'array'],
        ];
    }
}

<?php

namespace App\Http\Requests\Api\V1\Companies;

use App\Enums\CompanyCategoriesEnum;
use App\Enums\CompanyObligationsEnum;
use App\Enums\UserTypesEnum;
use Illuminate\Foundation\Http\FormRequest;

class CompanyStoreRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'company_name' => ['required', 'string', 'min:3', 'max:35', 'unique:companies,company_name'],
            'company_category' => ['required', 'in:' . CompanyCategoriesEnum::toString()],
            'company_obligation' => ['required', 'in:' . CompanyObligationsEnum::toString()],
            'company_emails' => ['required', 'array'],
            'owner_type' => ['required', 'in:' . UserTypesEnum::toString()],
            'voen' => ['required', 'integer', 'digits:10'],
            'voen_date' => ['required', 'date'],
            'dsmf_number' => ['required', 'integer', 'digits:13'],
            'charter_file' => ['required', 'file', 'mimes:png,jpg,jpeg,pdf,xlsx,xls,docx,doc'],
            'extract_file' => ['required', 'file', 'mimes:png,jpg,jpeg,pdf,xlsx,xls,docx,doc'],
            'main_user_id' => ['required', 'integer', 'exists:users,id'],
            'director_id_card_file' => ['required', 'file', 'mimes:png,jpg,jpeg,pdf,xlsx,xls,docx,doc'],
            'creators_files' => ['required', 'array'],
            'creators_files.*' => ['mimes:png,jpg,jpeg,pdf,xlsx,xls,docx,doc'],
            'asan_sign' => ['required', 'phone:AZ'],
            'asan_sign_start_date' => ['required', 'date'],
            'birth_id' => ['required', 'date'],
            'pin1' => ['required', 'integer', 'digits:4'],
            'pin2' => ['required', 'integer', 'digits:5'],
            'puk' => ['required', 'integer', 'digits:8'],
            'statistic_code' => ['required', 'integer', 'digits:8'],
            'statistic_password' => ['required', 'string', 'max:255'],
            'operator_azercell_account' => ['required', 'phone:AZ'],
            'operator_azercell_password' => ['required', 'max:255'],
            'ydm_account_email' => ['nullable', 'email:filter'],
            'ydm_password' => ['nullable', 'max:255'],
            'ydm_card_expired_at' => ['nullable', 'date']
        ];
    }
}

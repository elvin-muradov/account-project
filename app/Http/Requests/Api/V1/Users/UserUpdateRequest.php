<?php

namespace App\Http\Requests\Api\V1\Users;

use App\Enums\EducationTypesEnum;
use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserUpdateRequest extends FormRequest
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
        $user = User::query()->find($this->user);

        return [
            'name' => ['required', 'string', 'min:3', 'max:35'],
            'surname' => ['required', 'string', 'min:3', 'max:35'],
            'father_name' => ['required', 'string', 'min:3', 'max:35'],
            'email' => ['required', 'email:filter', 'unique:users,email,' . $this->user, 'max:255'],
            'password' => ['nullable', 'confirmed', 'string', 'min:8', 'max:16'],
            'phone' => ['required', 'string', 'unique:users,phone,' . $this->user, 'phone:AZ'],
            'birth_date' => ['required', 'date'],
            'education' => ['required', 'in:' . EducationTypesEnum::toString()],
            'education_files' => ['nullable', 'array', Rule::requiredIf(empty($user->education_files))],
            'education_files.*' => ['nullable', 'file', 'mimes:png,jpg,jpeg,pdf,docx,doc'],
            'certificate_files' => ['nullable', 'array'],
            'certificate_files.*' => ['nullable', 'file', 'mimes:png,jpg,jpeg,pdf,docx,doc'],
            'cv_files' => ['nullable', 'array', Rule::requiredIf(empty($user->cv_files))],
            'cv_files.*' => ['nullable', 'file', 'mimes:png,jpg,jpeg,pdf,docx,doc'],
            'self_photo_files' => ['nullable', 'array', Rule::requiredIf(empty($user->self_photo_files))],
            'self_photo_files.*' => ['nullable', 'file', 'mimes:png,jpg,jpeg,pdf,docx,doc'],
            'previous_job' => ['nullable', 'string', 'max:255'],
            'role_name' => ['nullable', 'string', 'in:department_head,accountant,leading_expert'],
            'delete_education_files' => ['sometimes', 'array'],
            'delete_cv_files' => ['sometimes', 'array'],
            'delete_certificate_files' => ['sometimes', 'array'],
            'delete_self_photo_files' => ['sometimes', 'array'],
        ];
    }
}

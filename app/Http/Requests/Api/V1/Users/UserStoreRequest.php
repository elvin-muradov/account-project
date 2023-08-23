<?php

namespace App\Http\Requests\Api\V1\Users;

use App\Enums\EducationTypesEnum;
use App\Enums\StatusTypesEnum;
use Illuminate\Foundation\Http\FormRequest;

class UserStoreRequest extends FormRequest
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
            'name' => ['required', 'string', 'min:3', 'max:35'],
            'surname' => ['required', 'string', 'min:3', 'max:35'],
            'father_name' => ['required', 'string', 'min:3', 'max:35'],
            'username' => ['required', 'string', 'unique:users,username', 'min:3', 'max:35', 'alpha_dash'],
            'email' => ['required', 'email:filter', 'unique:users,email', 'max:255'],
            'password' => ['required', 'confirmed', 'string', 'min:8', 'max:16'],
            'phone' => ['required', 'string', 'unique:users,phone', 'phone:AZ'],
            'birth_date' => ['required', 'date'],
            'education' => ['required', 'in:' . EducationTypesEnum::toString()],
            'education_files' => ['nullable', 'array'],
            'education_files.*' => ['mimes:png,jpg,jpeg,webp,pdf,docx', 'max:4096'],
            'certificate_files' => ['nullable', 'array'],
            'certificate_files.*' => ['mimes:png,jpg,jpeg,webp,pdf,docx', 'max:4096'],
            'cv_file' => ['nullable', 'file', 'mimes:pdf,docx', 'max:4096'],
            'self_photo_file' => ['nullable', 'file', 'mimes:png,jpeg,jpg,webp', 'max:4096'],
            'previous_job' => ['nullable', 'string', 'max:255'],
            'account_status' => ['nullable', 'in:' . StatusTypesEnum::toString()]
        ];
    }
}

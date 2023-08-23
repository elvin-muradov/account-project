<?php

namespace App\Http\Requests\Api\V1\Users;

use App\Enums\EducationTypesEnum;
use Illuminate\Foundation\Http\FormRequest;

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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:3', 'max:35'],
            'surname' => ['required', 'string', 'min:3', 'max:35'],
            'father_name' => ['required', 'string', 'min:3', 'max:35'],
            'email' => ['required', 'email:filter', 'unique:users,email,' . $this->user, 'max:255'],
            'password' => ['required', 'confirmed', 'string', 'min:8', 'max:16'],
            'phone' => ['required', 'string', 'unique:users,phone,' . $this->user, 'phone:AZ'],
            'birth_date' => ['required', 'date'],
            'education' => ['required', 'in:' . EducationTypesEnum::toString()],
            'education_files' => ['nullable', 'array'],
            'education_files.*' => ['file', 'mimes:png,jpg,jpeg,pdf,docx,doc'],
            'certificate_files' => ['nullable', 'array'],
            'certificate_files.*' => ['file', 'mimes:png,jpg,jpeg,pdf,docx,doc'],
            'cv_file' => ['nullable', 'file', 'mimes:png,jpg,jpeg,pdf,docx,doc'],
            'self_photo_file' => ['nullable', 'file', 'mimes:png,jpg,jpeg,pdf,docx,doc'],
            'previous_job' => ['nullable', 'string', 'max:255']
        ];
    }
}

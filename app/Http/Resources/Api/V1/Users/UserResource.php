<?php

namespace App\Http\Resources\Api\V1\Users;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'surname' => $this->surname,
            'father_name' => $this->father_name,
            'username' => $this->username,
            'phone' => $this->phone,
            'email' => $this->email,
            'birth_date' => Carbon::parse($this->birth_date)->format('d.m.Y'),
            'education' => $this->education,
            'education_files' => $this->education_files,
            'cv_files' => $this->cv_files,
            'self_photo_files' => $this->self_photo_files,
            'certificate_files' => $this->certificate_files,
            'previous_job' => $this->previous_job,
            'account_status' => $this->account_status,
            'created_at' => $this->created_at,
            'last_login_at' => $this->last_login_at,
            'roles' => $this->whenLoaded('roles')
        ];
    }
}

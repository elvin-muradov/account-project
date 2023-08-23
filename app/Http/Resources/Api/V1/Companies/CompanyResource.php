<?php

namespace App\Http\Resources\Api\V1\Companies;

use App\Http\Resources\Api\V1\Users\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CompanyResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => (int)$this->id,
            'company_name' => $this->company_name,
            'company_category' => $this->company_category,
            'company_obligation' => $this->company_obligation,
            'owner_type' => $this->owner_type,
            'company_emails' => json_decode($this->company_emails),
            'voen' => $this->voen,
            'voen_date' => $this->voen_date,
            'dsmf_number' => $this->dsmf_number,
            'charter_file' => json_decode($this->charter_file),
            'extract_file' => json_decode($this->extract_file),
            'main_user_id' => $this->main_user_id,
            'mainUser' => UserResource::make($this->whenLoaded('mainUser')),
            'director_id_card_file' => json_decode($this->director_id_card_file),
            'creators_files' => json_decode($this->creators_files),
            'asan_sign' => $this->asan_sign,
            'asan_sign_start_date' => $this->asan_sign_start_date,
            'birth_id' => $this->birth_id,
            'pin1' => $this->pin1,
            'pin2' => $this->pin2,
            'puk' => $this->puk,
            'statistic_code' => $this->statistic_code,
            'statistic_password' => $this->statistic_password,
            'operator_azercell_account' => $this->operator_azercell_account,
            'operator_azercell_password' => $this->operator_azercell_password,
            'ydm_account_email' => $this->ydm_account_email,
            'ydm_password' => $this->ydm_password,
            'ydm_card_expired_at' => $this->ydm_card_expired_at
        ];
    }
}

<?php

namespace App\Http\Resources\Api\V1\Companies;

use App\Http\Resources\Api\V1\Employee\EmployeeResource;
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
            'company_address' => $this->company_address,
            'owner_type' => $this->owner_type,
            'company_emails' => $this->company_emails,
            'tax_id_number' => $this->tax_id_number,
            'tax_id_number_date' => $this->tax_id_number_date,
            'tax_id_number_files' => $this->tax_id_number_files,
            'dsmf_number' => $this->dsmf_number,
            'charter_files' => $this->charter_files,
            'extract_files' => $this->extract_files,
            'main_user_id' => $this->main_user_id,
            'main_user' => EmployeeResource::make($this->whenLoaded('mainUser')),
            'director_id' => $this->director_id,
            'director' => EmployeeResource::make($this->whenLoaded('director')),
            'director_id_card_files' => $this->director_id_card_files,
            'creators_files' => $this->creators_files,
            'fixed_asset_files' => $this->fixed_asset_files,
            'founding_decision_files' => $this->founding_decision_files,
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
            'ydm_card_expired_at' => $this->ydm_card_expired_at,
            'activity_codes' => $this->whenLoaded('activityCodes'),
            'created_at' => $this->created_at,
            'accountant' => $this->whenLoaded('accountant') ?
                $this->accountant?->only('id', 'name', 'surname') : null,
            'accountant_assign_date' => $this->accountant_assign_date
        ];
    }
}

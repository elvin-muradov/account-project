<?php

namespace App\Traits;

use App\Enums\UserTypesEnum;
use App\Models\Company\Company;
use Illuminate\Http\JsonResponse;

trait HasCompaniesServed
{
    use HttpResponses;

    protected array $responses = [
        'INDIVIDUAL' => 1,
        'NOT_FOUND_INDIVIDUAL' => 2,
        'LEGAL' => 3,
        'NOT_FOUND_LEGAL' => 4,
        'SUCCESS' => 5
    ];

    public function syncCompaniesServed(array $companies): int|null
    {
        $companies = array_unique($companies);

        $selectedInvididualCompanies = Company::query()
            ->whereIn('id', $companies)
            ->where('owner_type', '=', UserTypesEnum::INDIVIDUAL)
            ->whereNull('accountant_id')
            ->get();

        $selectedLegalCompanies = Company::query()
            ->whereIn('id', $companies)
            ->where('owner_type', '=', UserTypesEnum::LEGAL)
            ->whereNull('accountant_id')
            ->get();

        $accountantInvidiualCompanies = Company::query()
            ->where('accountant_id', '=', $this->id)
            ->where('owner_type', '=', UserTypesEnum::INDIVIDUAL)
            ->get();

        $accountantLegalCompanies = Company::query()
            ->where('accountant_id', '=', $this->id)
            ->where('owner_type', '=', UserTypesEnum::LEGAL)
            ->get();

        if (
            $accountantInvidiualCompanies->count() < 10
        ) {
            foreach ($selectedInvididualCompanies as $company) {
                $company->update([
                    'accountant_id' => $this->id
                ]);
            }
        } else {

            return $this->responses['INDIVIDUAL'];
        }

        if (
            $accountantLegalCompanies->count() < 5
        ) {
            foreach ($selectedLegalCompanies as $company) {
                $company->update([
                    'accountant_id' => $this->id
                ]);
            }
        } else {
            return $this->responses['LEGAL'];
        }

        return $this->responses['SUCCESS'];
    }
}

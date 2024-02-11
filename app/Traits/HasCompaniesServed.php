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

        if ($selectedInvididualCompanies->count() === 0) {
            return $this->responses['NOT_FOUND_INDIVIDUAL'];
        }

        if ($selectedLegalCompanies->count() === 0) {
            return $this->responses['NOT_FOUND_LEGAL'];
        }

        $accountantInvidiualCompanies = Company::query()
            ->where('accountant_id', '=', $this->id)
            ->where('owner_type', '=', UserTypesEnum::INDIVIDUAL)
            ->get();

        $accountantLegalCompanies = Company::query()
            ->where('accountant_id', '=', $this->id)
            ->where('owner_type', '=', UserTypesEnum::LEGAL)
            ->get();

        if (
            $accountantInvidiualCompanies->count() < 10 &&
            $selectedInvididualCompanies->count() < $accountantInvidiualCompanies->count()
        ) {
            $selectedInvididualCompanies->each(function ($company) {
                $company->accountant_id = $this->id;
            });
        } else {
            return $this->responses['INDIVIDUAL'];
        }

        if (
            $accountantLegalCompanies->count() < 5 &&
            $selectedLegalCompanies->count() < $accountantLegalCompanies->count()
        ) {
            $selectedLegalCompanies->each(function ($company) {
                $company->accountant_id = $this->id;
            });
        } else {
            return $this->responses['LEGAL'];
        }

        return $this->responses['SUCCESS'];
    }
}

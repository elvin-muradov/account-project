<?php

namespace App\Http\Controllers\Api\V1\Companies;

use App\Enums\CompanyMainDocuments;
use App\Http\Controllers\Controller;
use App\Models\Company\Company;
use App\Traits\HttpResponses;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MainDocumentController extends Controller
{
    use HttpResponses;

    public function companyMainDocuments(Request $request, $company): JsonResponse
    {
        $request->validate([
            'type' => ['required', 'string', 'in:' . CompanyMainDocuments::toString()]
        ]);

        $company = Company::query()->find($company);

        $type = $request->input('type');

        if ($company) {
            return match ($type) {
                CompanyMainDocuments::tax_id_number_files->value => $this->success(data: $company->tax_id_number_files),
                CompanyMainDocuments::charter_files->value => $this->success(data: $company->charter_files),
                CompanyMainDocuments::extract_files->value => $this->success(data: $company->extract_files),
                CompanyMainDocuments::director_id_card_files->value => $this
                    ->success(data: $company->director_id_card_files),
                CompanyMainDocuments::creators_files->value => $this->success(data: $company->creators_files),
                CompanyMainDocuments::fixed_asset_files->value => $this->success(data: $company->fixed_asset_files),
                CompanyMainDocuments::founding_decision_files->value => $this
                    ->success(data: $company->founding_decision_files),
                default => $this->error(message: 'Fayllar tapılmadı', code: 404),
            };
        } else {
            return $this->error(message: 'Şirkət tapılmadı', code: 404);
        }
    }
}

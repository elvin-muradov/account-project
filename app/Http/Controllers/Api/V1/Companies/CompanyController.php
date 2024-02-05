<?php

namespace App\Http\Controllers\Api\V1\Companies;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Companies\CompanyStoreRequest;
use App\Http\Requests\Api\V1\Companies\CompanyUpdateRequest;
use App\Http\Resources\Api\V1\Companies\CompanyCollection;
use App\Http\Resources\Api\V1\Companies\CompanyResource;
use App\Models\Company\Company;
use App\Traits\HttpResponses;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CompanyController extends Controller
{
    use HttpResponses;

    public function index(Request $request): JsonResponse
    {
        $companies = Company::query()->with(['mainUser', 'director'])
            ->paginate($request->limit ?? 10);

        return $this->success(data: new CompanyCollection($companies));
    }

    public function store(CompanyStoreRequest $request): JsonResponse
    {
        $data = $request->validated();

        if ($request->hasFile('tax_id_number_files')) {
            $tinFiles = $request->file('tax_id_number_files');
            $data = array_merge($data, ['tax_id_number_files' => returnFilesArray($tinFiles, 'tax_id_number_files')]);
        }
        if ($request->hasFile('charter_files')) {
            $charterFiles = $request->file('charter_files');
            $data = array_merge($data, ['charter_files' => returnFilesArray($charterFiles, 'charter_files')]);
        }
        if ($request->hasFile('extract_files')) {
            $extractFiles = $request->file('extract_files');
            $data = array_merge($data, ['extract_files' => returnFilesArray($extractFiles, 'extract_files')]);
        }
        if ($request->hasFile('director_id_card_files')) {
            $idCardFiles = $request->file('director_id_card_files');
            $data = array_merge($data, ['director_id_card_files' => returnFilesArray($idCardFiles,
                'director_id_card_files')]);
        }
        if ($request->hasFile('creators_files')) {
            $creatorFiles = $request->file('creators_files', []);
            $data = array_merge($data, ['creators_files' => returnFilesArray($creatorFiles,
                'creators_files')]);
        }
        if ($request->hasFile('founding_decision_files')) {
            $foundingDecisionFiles = $request->file('founding_decision_files', []);
            $data = array_merge($data, ['founding_decision_files' => returnFilesArray($foundingDecisionFiles,
                'founding_decision_files')]);
        }
        if ($request->hasFile('fixed_asset_files')) {
            $fixedAssetFiles = $request->file('fixed_asset_files', []);
            $data = array_merge($data, ['fixed_asset_files' => returnFilesArray($fixedAssetFiles,
                'fixed_asset_files')]);
        }

        $company = Company::query()->create($data);

        return $this
            ->success(data: CompanyResource::make($company), message: "Şirkət uğurla əlavə olundu");
    }

    public function show($company): JsonResponse
    {
        $company = Company::query()->with(['mainUser', 'employees', 'activityCodes', 'director'])->find($company);

        if ($company) {
            return $this->success(CompanyResource::make($company));
        } else {
            return $this->error(message: "Şirkət tapılmadı", code: 404);
        }
    }

    public function update(CompanyUpdateRequest $request, $company): JsonResponse
    {
        $data = $request->validated();

        $company = Company::query()->find($company);

        if (!$company) {
            return $this->error(message: "Şirkət tapılmadı", code: 404);
        }

        if ($request->has('delete_tax_id_number_files') && $request->delete_tax_id_number_files != null) {
            $deletedTinFiles = $request->input('delete_tax_id_number_files');
            $tinFiles = $company->tax_id_number_files ?? [];
            $deletedFiles = deleteFiles($deletedTinFiles, $tinFiles, true);
            if (is_array($deletedFiles)) {
                $company->tax_id_number_files = array_values($deletedFiles);
            } else {
                return $this->error(
                    message: "Ən az bir faylın qalması vacibdir", code: 400
                );
            }
        }
        if ($request->has('delete_charter_files') && $request->delete_charter_files != null) {
            $deletedCharterFiles = $request->input('delete_charter_files');
            $charterFiles = $company->charter_files ?? [];
            $deletedFiles = deleteFiles($deletedCharterFiles, $charterFiles, true);
            if (is_array($deletedFiles)) {
                $company->charter_files = array_values($deletedFiles);
            } else {
                return $this->error(
                    message: "Ən az bir faylın qalması vacibdir", code: 400
                );
            }
        }
        if ($request->has('delete_extract_files') && $request->delete_extract_files != null) {
            $deletedExtractFiles = $request->input('delete_extract_files');
            $extractFiles = $company->extract_files ?? [];
            $deletedFiles = deleteFiles($deletedExtractFiles, $extractFiles, true);
            if (is_array($deletedFiles)) {
                $company->extract_files = array_values($deletedFiles);
            } else {
                return $this->error(
                    message: "Ən az bir faylın qalması vacibdir", code: 400
                );
            }
        }
        if ($request->has('delete_director_id_card_files') && $request->delete_director_id_card_files != null) {
            $deletedDirectorCardFiles = $request->input('delete_director_id_card_files');
            $directorCardFiles = $company->director_id_card_files ?? [];
            $deletedFiles = deleteFiles($deletedDirectorCardFiles, $directorCardFiles, true);
            if (is_array($deletedFiles)) {
                $company->director_id_card_files = array_values($deletedFiles);
            } else {
                return $this->error(
                    message: "Ən az bir faylın qalması vacibdir", code: 400
                );
            }
        }
        if ($request->has('delete_creators_files') && $request->delete_creators_files != null) {
            $deletedCreatorsFiles = $request->input('delete_creators_files');
            $creatorsFiles = $company->creators_files ?? [];
            $deletedFiles = deleteFiles($deletedCreatorsFiles, $creatorsFiles, true);
            if (is_array($deletedFiles)) {
                $company->creators_files = array_values($deletedFiles);
            } else {
                return $this->error(
                    message: "Ən az bir faylın qalması vacibdir", code: 400
                );
            }
        }
        if ($request->has('delete_founding_decision_files') && $request->delete_founding_decision_files != null) {
            $deletedFoundingDecisionFiles = $request->input('delete_founding_decision_files');
            $foundingDecisionFiles = $company->founding_decision_files ?? [];
            $deletedFiles = deleteFiles($deletedFoundingDecisionFiles, $foundingDecisionFiles, true);
            if (is_array($deletedFiles)) {
                $company->founding_decision_files = array_values($deletedFiles);
            } else {
                return $this->error(
                    message: "Ən az bir faylın qalması vacibdir", code: 400
                );
            }
        }
        if ($request->has('delete_fixed_asset_files') && $request->delete_fixed_asset_files != null) {
            $deletedFixedAssetFiles = $request->input('delete_fixed_asset_files');
            $fixedAssetFiles = $company->fixed_asset_files ?? [];
            $deletedFiles = deleteFiles($deletedFixedAssetFiles, $fixedAssetFiles, true);
            if (is_array($deletedFiles)) {
                $company->fixed_asset_files = array_values($deletedFiles);
            } else {
                return $this->error(
                    message: "Ən az bir faylın qalması vacibdir", code: 400
                );
            }
        }

        if ($request->hasFile('tax_id_number_files')) {
            $tinFiles = $request->file('tax_id_number_files');
            $tinFilesArr = $company->tax_id_number_files ?? [];
            $updatedFiles = returnFilesArray($tinFiles, 'tax_id_number_files');
            $data = array_merge($data, ['tax_id_number_files' => array_merge($tinFilesArr, $updatedFiles)]);
        }
        if ($request->hasFile('charter_files')) {
            $charterFiles = $request->file('charter_files');
            $charterFilesArr = $company->charter_files ?? [];
            $updatedFiles = returnFilesArray($charterFiles, 'charter_files');
            $data = array_merge($data, ['charter_files' => array_merge($charterFilesArr, $updatedFiles)]);
        }
        if ($request->hasFile('extract_files')) {
            $extractFiles = $request->file('extract_files');
            $extractFilesArr = $company->extract_files ?? [];
            $updatedFiles = returnFilesArray($extractFiles, 'extract_files');
            $data = array_merge($data, ['extract_files' => array_merge($extractFilesArr, $updatedFiles)]);
        }
        if ($request->hasFile('director_id_card_files')) {
            $directorCardFiles = $request->file('director_id_card_files');
            $directorCardFilesArr = $company->director_id_card_files ?? [];
            $updatedFiles = returnFilesArray($directorCardFiles, 'director_id_card_files');
            $data = array_merge($data, ['director_id_card_files' => array_merge($directorCardFilesArr, $updatedFiles)]);
        }
        if ($request->hasFile('creators_files')) {
            $creatorsFiles = $request->file('creators_files');
            $creatorsFilesArr = $company->creators_files ?? [];
            $updatedFiles = returnFilesArray($creatorsFiles, 'creators_files');
            $data = array_merge($data, ['creators_files' => array_merge($creatorsFilesArr, $updatedFiles)]);
        }
        if ($request->hasFile('founding_decision_files')) {
            $foundingDecisionFiles = $request->file('founding_decision_files');
            $foundingDecisionFilesArr = $company->founding_decision_files ?? [];
            $updatedFiles = returnFilesArray($foundingDecisionFiles, 'founding_decision_files');
            $data = array_merge($data, ['founding_decision_files' => array_merge($foundingDecisionFilesArr,
                $updatedFiles)]);
        }
        if ($request->hasFile('fixed_asset_files')) {
            $fixedAssetFiles = $request->file('fixed_asset_files');
            $fixedAssetFilesArr = $company->fixed_asset_files ?? [];
            $updatedFiles = returnFilesArray($fixedAssetFiles, 'fixed_asset_files');
            $data = array_merge($data, ['fixed_asset_files' => array_merge($fixedAssetFilesArr, $updatedFiles)]);
        }

        $company->update($data);

        return $this->success(data: CompanyResource::make($company), message: "Şirkət uğurla yeniləndi");
    }

    public function destroy($company): JsonResponse
    {
        $company = Company::query()->find($company);

        if (!$company) {
            return $this->error(message: "Şirkət tapılmadı", code: 404);
        }

        if ($company->tax_id_number_files != null && count($company->tax_id_number_files) > 0) {
            checkFilesAndDeleteFromStorage($company->tax_id_number_files);
        }

        if ($company->charter_files != null && count($company->charter_files) > 0) {
            checkFilesAndDeleteFromStorage($company->charter_files);
        }

        if ($company->extract_files != null && count($company->extract_files) > 0) {
            checkFilesAndDeleteFromStorage($company->extract_files);
        }

        if ($company->director_id_card_files != null && count($company->director_id_card_files) > 0) {
            checkFilesAndDeleteFromStorage($company->director_id_card_files);
        }

        if ($company->creators_files != null && count($company->creators_files) > 0) {
            checkFilesAndDeleteFromStorage($company->creators_files);
        }

        if ($company->founding_decision_files != null && count($company->founding_decision_files) > 0) {
            checkFilesAndDeleteFromStorage($company->founding_decision_files);
        }

        if ($company->fixed_asset_files != null && count($company->fixed_asset_files) > 0) {
            checkFilesAndDeleteFromStorage($company->fixed_asset_files);
        }

        $company->delete();

        return $this->success(message: "Şirkət uğurla silindi");
    }
}

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
        $companies = Company::query()->with('mainUser')->paginate($request->limit ?? 10);

        return $this->success(data: new CompanyCollection($companies));
    }

    public function store(CompanyStoreRequest $request): JsonResponse
    {
        $charterFilePath = '';
        $extractFilePath = '';
        $idCardFilePath = '';
        $creatorFilesPath = [];

        if ($request->hasFile('charter_file')) {
            $charterFile = $request->file('charter_file');
            $charterFileName = 'CHARTER-FILE-' . uniqid() . '.' . $charterFile->getClientOriginalExtension();
            $charterFilePath = Storage::putFileAs('public/charter_files', $charterFile, $charterFileName);
        }

        if ($request->hasFile('extract_file')) {
            $extractFile = $request->file('extract_file');
            $extractFileName = 'EXTRACT-FILE-' . uniqid() . '.' . $extractFile->getClientOriginalExtension();
            $extractFilePath = Storage::putFileAs('public/extract_files', $extractFile, $extractFileName);
        }

        if ($request->hasFile('director_id_card_file')) {
            $idCardFile = $request->file('director_id_card_file');
            $idCardFileName = 'ID-CARD-FILE-' . uniqid() . '.' . $extractFile->getClientOriginalExtension();
            $idCardFilePath = Storage::putFileAs('public/director_id_card_files', $idCardFile, $idCardFileName);
        }

        if ($request->hasFile('creators_files', [])) {
            $creatorFiles = $request->file('creators_files', []);

            foreach ($creatorFiles as $file) {
                $creatorFileName = 'ID-CARD-FILE-' . uniqid() . '.' . $file->getClientOriginalExtension();
                $creatorFilePath = Storage::putFileAs('public/creators_files', $file, $creatorFileName);
                $creatorFilesPath[] = $creatorFilePath;
            }
        }

        if ($request->hasFile('director_id_card_file')) {
            $idCardFile = $request->file('director_id_card_file');
            $idCardFileName = 'ID-CARD-FILE-' . uniqid() . '.' . $extractFile->getClientOriginalExtension();
            $idCardFilePath = Storage::putFileAs('public/director_id_card_files', $idCardFile, $idCardFileName);
        }

        $company = Company::query()->create([
            'company_name' => $request->input('company_name'),
            'company_category' => $request->input('company_category'),
            'company_obligation' => $request->input('company_obligation'),
            'company_emails' => json_encode($request->input('company_emails')),
            'owner_type' => $request->input('owner_type'),
            'charter_file' => json_encode($charterFilePath),
            'extract_file' => json_encode($extractFilePath),
            'director_id_card_file' => json_encode($idCardFilePath),
            'creators_files' => json_encode($creatorFilesPath),
            'voen' => $request->input('voen'),
            'voen_date' => $request->input('voen_date'),
            'dsmf_number' => $request->input('dsmf_number'),
            'main_user_id' => $request->input('main_user_id'),
            'asan_sign' => $request->input('asan_sign'),
            'asan_sign_start_date' => $request->input('asan_sign_start_date'),
            'birth_id' => $request->input('birth_id'),
            'pin1' => $request->input('pin1'),
            'pin2' => $request->input('pin2'),
            'puk' => $request->input('puk'),
            'statistic_code' => $request->input('statistic_code'),
            'statistic_password' => $request->input('statistic_password'),
            'operator_azercell_account' => $request->input('operator_azercell_account'),
            'operator_azercell_password' => $request->input('operator_azercell_password'),
            'ydm_account_email' => $request->input('ydm_account_email'),
            'ydm_password' => $request->input('ydm_password'),
            'ydm_card_expired_at' => $request->input('ydm_card_expired_at'),
        ]);

        return $this
            ->success(data: CompanyResource::make($company), message: "Şirkət uğurla əlavə olundu");
    }

    public function show($company): JsonResponse
    {
        $company = Company::query()->with('mainUser')->find($company);

        if ($company) {
            return $this->success(CompanyResource::make($company));
        } else {
            return $this->error(message: "Şirkət tapılmadı", code: 404);
        }
    }

    public function update(CompanyUpdateRequest $request, $company): JsonResponse
    {
        $company = Company::query()->find($company);

        if ($company) {
            if ($request->hasFile('charter_file')) {
                $charterFile = $request->file('charter_file');
                $charterFileName = 'CHARTER-FILE-' . uniqid() . '.' . $charterFile->getClientOriginalExtension();
                $charterFilePath = Storage::putFileAs('public/charter_files', $charterFile, $charterFileName);
            }

            if ($request->hasFile('extract_file')) {
                $extractFile = $request->file('extract_file');
                $extractFileName = 'EXTRACT-FILE-' . uniqid() . '.' . $extractFile->getClientOriginalExtension();
                $extractFilePath = Storage::putFileAs('public/extract_files', $extractFile, $extractFileName);
            }

            if ($request->hasFile('director_id_card_file')) {
                $idCardFile = $request->file('director_id_card_file');
                $idCardFileName = 'ID-CARD-FILE-' . uniqid() . '.' . $extractFile->getClientOriginalExtension();
                $idCardFilePath = Storage::putFileAs('public/director_id_card_files', $idCardFile, $idCardFileName);
            }

            if ($request->hasFile('creators_files', [])) {
                $creatorFiles = $request->file('creators_files', []);

                foreach ($creatorFiles as $file) {
                    $creatorFileName = 'ID-CARD-FILE-' . uniqid() . '.' . $file->getClientOriginalExtension();
                    $creatorFilePath = Storage::putFileAs('public/creators_files', $file, $creatorFileName);
                    $creatorFilesPath[] = $creatorFilePath;
                }
            }

            if ($request->hasFile('director_id_card_file')) {
                $idCardFile = $request->file('director_id_card_file');
                $idCardFileName = 'ID-CARD-FILE-' . uniqid() . '.' . $extractFile->getClientOriginalExtension();
                $idCardFilePath = Storage::putFileAs('public/director_id_card_files', $idCardFile, $idCardFileName);
            }

            $company->update([
                'company_name' => $request->input('company_name'),
                'company_category' => $request->input('company_category'),
                'company_obligation' => $request->input('company_obligation'),
                'company_emails' => json_encode($request->input('company_emails')),
                'owner_type' => $request->input('owner_type'),
                'charter_file' => json_encode($charterFilePath),
                'extract_file' => json_encode($extractFilePath),
                'director_id_card_file' => json_encode($idCardFilePath),
                'creators_files' => json_encode($creatorFilesPath),
                'voen' => $request->input('voen'),
                'voen_date' => $request->input('voen_date'),
                'dsmf_number' => $request->input('dsmf_number'),
                'main_user_id' => $request->input('main_user_id'),
                'asan_sign' => $request->input('asan_sign'),
                'asan_sign_start_date' => $request->input('asan_sign_start_date'),
                'birth_id' => $request->input('birth_id'),
                'pin1' => $request->input('pin1'),
                'pin2' => $request->input('pin2'),
                'puk' => $request->input('puk'),
                'statistic_code' => $request->input('statistic_code'),
                'statistic_password' => $request->input('statistic_password'),
                'operator_azercell_account' => $request->input('operator_azercell_account'),
                'operator_azercell_password' => $request->input('operator_azercell_password'),
                'ydm_account_email' => $request->input('ydm_account_email'),
                'ydm_password' => $request->input('ydm_password'),
                'ydm_card_expired_at' => $request->input('ydm_card_expired_at'),
            ]);

            return $this->success(data: CompanyResource::make($company),message: "Şirkət uğurla yeniləndi");
        } else {
            return $this->error(message: "Şirkət tapılmadı", code: 404);
        }
    }


    public function destroy($company)
    {
        $company = Company::query()->find($company);

        if ($company) {
            $company->delete();
            return $this->success(message: "Şirkət uğurla silindi");
        } else {
            return $this->error(message: "Şirkət tapılmadı", code: 404);
        }
    }
}

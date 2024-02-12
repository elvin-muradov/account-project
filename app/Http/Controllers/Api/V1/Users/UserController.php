<?php

namespace App\Http\Controllers\Api\V1\Users;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Users\UserStoreRequest;
use App\Http\Requests\Api\V1\Users\UserUpdateRequest;
use App\Http\Resources\Api\V1\Users\UserCollection;
use App\Http\Resources\Api\V1\Users\UserResource;
use App\Models\User;
use App\Traits\HttpResponses;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    use HttpResponses;

    public function index(Request $request): JsonResponse
    {
        $users = User::query()
            ->with('roles')
            ->paginate($request->limit ?? 10);

        return $this->success(data: new UserCollection($users));
    }

    public function store(UserStoreRequest $request): JsonResponse
    {
        $data = $request->validated();
        $lowerCases = array_map('strtolower', $request->only('email', 'username'));
        $password = ['password' => Hash::make($request->password)];
        $data = array_merge($data, $lowerCases, $password);

        if ($request->hasFile('education_files', [])) {
            $eduFiles = $request->file('education_files');
            $data = array_merge($data, ['education_files' => returnFilesArray($eduFiles, 'education_files')]);
        }

        if ($request->hasFile('cv_files')) {
            $cvFiles = $request->file('cv_files');
            $data = array_merge($data, ['cv_files' => returnFilesArray($cvFiles, 'cv_files')]);
        }

        if ($request->hasFile('self_photo_files')) {
            $ppPhotos = $request->file('self_photo_files');
            $data = array_merge($data, ['self_photo_files' => returnFilesArray($ppPhotos, 'self_photo_files')]);
        }

        if ($request->hasFile('certificate_files')) {
            $ctFiles = $request->file('certificate_files');
            $data = array_merge($data, ['certificate_files' => returnFilesArray($ctFiles, 'certificate_files')]);
        }

        DB::beginTransaction();
        $user = User::query()->create($data);

        if ($request->input('role_name') != null) {
            $role = Role::query()->where('name', '=', $request->input('role_name'))->first();
            if ($role) {
                $user->assignRole($role);
            }
        }

        $assignResult = null;

        if (is_array($request->input('company_ids')) && !empty($request->input('company_ids'))) {
            $assignResult = $user->assignCompanies($request->input('company_ids'));
        }

        switch ($assignResult) {
            case 2:
                DB::rollBack();
                return $this->error(message: "Ən çox 10 fiziki şirkətə xidmət göstərilə bilər", code: 400);
            case 3:
                DB::rollBack();
                return $this->error(message: "Ən çox 5 hüquqi şirkətə xidmət göstərilə bilər", code: 400);
            default:
                DB::commit();
                return $this->success(data: UserResource::make($user), message: 'İstifadəçi uğurla əlavə olundu');
        }
    }

    public function update(UserUpdateRequest $request, $user): JsonResponse
    {
        $data = $request->validated();
        $lowerCases = array_map('strtolower', $request->only('email', 'username'));
        $data = array_merge($data, $lowerCases);

        if ($request->has('password') && $request->password != null &&
            $request->password != '' && trim($request->password) != '') {

            $password = ['password' => Hash::make($request->password)];
            $data = array_merge($data, $lowerCases, $password);
        }

        $user = User::query()->find($user);

        if (!$user) {
            return $this->error(message: "İstifadəçi tapılmadı", code: 404);
        }

        DB::beginTransaction();

        if ($request->has('delete_education_files') && $request->delete_education_files != null) {
            $deletedEduFiles = $request->input('delete_education_files', []);
            $educationFiles = $user->education_files ?? [];
            $deletedFiles = deleteFiles($deletedEduFiles, $educationFiles);
            $user->education_files = array_values($deletedFiles);
        }
        if ($request->has('delete_cv_files') && $request->delete_cv_files != null) {
            $deletedCVFiles = $request->input('delete_cv_files', []);
            $cvFiles = $user->cv_files ?? [];
            $deletedFiles = deleteFiles($deletedCVFiles, $cvFiles);
            $user->cv_files = array_values($deletedFiles);
        }
        if ($request->has('delete_self_photo_files') && $request->delete_self_photo_files != null) {
            $deletedPPs = $request->input('delete_self_photo_files', []);
            $ppFiles = $user->self_photo_files ?? [];
            $deletedFiles = deleteFiles($deletedPPs, $ppFiles);
            $user->self_photo_files = array_values($deletedFiles);
        }
        if ($request->has('delete_certificate_files') && $request->delete_certificate_files != null) {
            $deletedCTFiles = $request->input('delete_certificate_files', []);
            $ctFiles = $user->certificate_files ?? [];
            $deletedFiles = deleteFiles($deletedCTFiles, $ctFiles);
            $user->certificate_files = array_values($deletedFiles);
        }

        if ($request->hasFile('education_files')) {
            $eduFiles = $request->file('education_files');
            $eduFilesArr = $user->education_files;
            $updatedFiles = returnFilesArray($eduFiles, 'education_files');
            $data = array_merge($data, ['education_files' => array_merge($eduFilesArr, $updatedFiles)]);
        }
        if ($request->hasFile('cv_files')) {
            $cvFiles = $request->file('cv_files');
            $cvArr = $user->cv_files;
            $updatedFiles = returnFilesArray($cvFiles, 'cv_files');
            $data = array_merge($data, ['cv_files' => array_merge($cvArr, $updatedFiles)]);
        }
        if ($request->hasFile('self_photo_files')) {
            $ppPhotos = $request->file('self_photo_files');
            $ppPhotosArr = $user->self_photo_files;
            $updatedFiles = returnFilesArray($ppPhotos, 'self_photo_files');
            $data = array_merge($data, ['self_photo_files' => array_merge($ppPhotosArr, $updatedFiles)]);
        }
        if ($request->hasFile('certificate_files')) {
            $ctFiles = $request->file('certificate_files');
            $ctFileUrls = $user->certificate_files;
            $updatedFiles = returnFilesArray($ctFiles, 'certificate_files');
            $data = array_merge($data, ['certificate_files' => array_merge($ctFileUrls, $updatedFiles)]);
        }

        $user->update($data);

        if ($request->input('role_name') !== null) {
            $role = Role::query()->where('name', $request->input('role_name'))->first();
            if ($role) {
                $user->syncRoles($role);
            }
        }

        $assignResult = null;

        if (is_array($request->input('company_ids')) && !empty($request->input('company_ids'))) {
            $assignResult = $user->assignCompanies($request->input('company_ids'));
        }

        $user->refresh();

        switch ($assignResult) {
            case 2:
                DB::rollBack();
                return $this->error(message: "Ən çox 10 fiziki şirkətə xidmət göstərilə bilər", code: 400);
            case 3:
                DB::rollBack();
                return $this->error(message: "Ən çox 5 hüquqi şirkətə xidmət göstərilə bilər", code: 400);
            default:
                DB::commit();
                return $this->success(data: UserResource::make($user), message: "İstifadəçi uğurla yeniləndi");
        }
    }

    public function show($user): JsonResponse
    {
        $user = User::query()
            ->with(['roles', 'companiesServed'])
            ->find($user);

        if (!$user) {
            return $this->error(message: "İstifadəçi tapılmadı", code: 404);
        }

        return $this->success(data: UserResource::make($user));
    }

    public function destroy($user): JsonResponse
    {
        $user = User::query()->find($user);

        if (!$user) {
            return $this->error(message: "İstifadəçi tapılmadı", code: 404);
        }

        if ($user->cv_files != null && count($user->cv_files) > 0) {
            checkFilesAndDeleteFromStorage($user->cv_files);
        }

        if ($user->education_files != null && count($user->education_files) > 0) {
            checkFilesAndDeleteFromStorage($user->education_files);
        }

        if ($user->self_photo_files != null && count($user->self_photo_files) > 0) {
            checkFilesAndDeleteFromStorage($user->self_photo_files);
        }

        if ($user->certificate_files != null && count($user->certificate_files) > 0) {
            checkFilesAndDeleteFromStorage($user->certificate_files);
        }

        $user->delete();

        return $this->success(message: "İstifadəçi uğurla silindi", code: 200);
    }

    public function changeStatusOfUser(Request $request, $user): JsonResponse
    {
        $request->validate([
            'account_status' => ['required', 'in:APPROVED,PENDING,REJECTED']
        ]);

        $user = User::query()->find($user);

        if (!$user) {
            return $this->error(message: "İstifadəçi tapılmadı", code: 404);
        }

        $user->update(['account_status' => $request->account_status]);

        return $this->success(message: "İstifadəçinin statusu uğurla yeniləndi");
    }
}

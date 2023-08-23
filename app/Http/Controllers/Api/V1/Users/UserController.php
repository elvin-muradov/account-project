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
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    use HttpResponses;

    public function index(Request $request): JsonResponse
    {
        $users = User::query()->paginate($request->limit ?? 10);

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

            $eduFilesArr = [];

            foreach ($eduFiles as $eduFile) {
                $eduFileName = 'EDU-file' . uniqid() . '.' . $eduFile->getClientOriginalExtension();
                $eduFileUrl = Storage::putFileAs('public/education_files', $eduFile, $eduFileName);
                $eduFilesArr[] = $eduFileUrl;
            }

            $edu = ['education_files' => json_encode($eduFilesArr)];
            $data = array_merge($data, $edu);
        }

        if ($request->hasFile('cv_file')) {
            $cvFile = $request->file('cv_file');
            $cvFileName = 'CV-file' . uniqid() . '.' . $cvFile->getClientOriginalExtension();
            $cvFileUrl = Storage::putFileAs('public/cv_files', $cvFile, $cvFileName);
            $cv = ['cv_file' => json_encode($cvFileUrl)];
            $data = array_merge($data, $cv);
        }

        if ($request->hasFile('self_photo_file')) {
            $ppPhoto = $request->file('self_photo_file');
            $ppName = 'PP-' . uniqid() . '.' . $ppPhoto->getClientOriginalExtension();
            $ppPhotoUrl = Storage::putFileAs('public/profile_photos', $ppPhoto, $ppName);
            $pp = ['self_photo_file' => json_encode($ppPhotoUrl)];
            $data = array_merge($data, $pp);
        }

        if ($request->hasFile('certificate_files', [])) {
            $ctFiles = $request->file('certificate_files');

            $ctFileUrls = [];

            foreach ($ctFiles as $ctFileUrl) {
                $ctFileName = 'Certificate-file' . uniqid() . '.' . $ctFileUrl->getClientOriginalExtension();
                $ctFileUPath = Storage::putFileAs('public/certificate_files', $ctFileUrl, $ctFileName);
                $ctFileUrls[] = $ctFileUPath;
            }

            $ct = ['certificate_files' => json_encode($ctFileUrls)];
            $data = array_merge($data, $ct);
        }

        $user = User::query()->create($data);

        return $this->success(data: $user, message: 'İstifadəçi uğurla əlavə olundu');
    }

    public function update(UserUpdateRequest $request, $user): JsonResponse
    {
        $data = $request->validated();
        $lowerCases = array_map('strtolower', $request->only('email', 'username'));
        $data = array_merge($data, $lowerCases);

        if ($request->has('password') && $request->password) {
            $password = ['password' => Hash::make($request->password)];
            $data = array_merge($data, $lowerCases, $password);
        }

        $user = User::query()->find($user);

        if ($user != null) {
            if ($request->has('delete_education_files')) {
                $deletedEduFiles = $request->delete_education_files;

                foreach ($deletedEduFiles as $file) {
                    if (Storage::exists('public/education_files/' . $file)) {
                        Storage::delete('public/education_files/' . $file);
                    }
                }
            }

            if ($request->has('delete_cv_file')) {
                $deletedCVFile = $request->delete_cv_file;

                if (Storage::exists('public/cv_files/' . $deletedCVFile)) {
                    Storage::delete('public/cv_files/' . $deletedCVFile);
                }
            }

            if ($request->has('delete_self_photo_file')) {
                $deletedPP = $request->delete_self_photo_file;

                if (Storage::exists('public/profile_photos/' . $deletedPP)) {
                    Storage::delete('public/profile_photos/' . $deletedPP);
                }
            }

            if ($request->has('delete_certificate_files')) {
                $deletedCTFiles = $request->input('delete_certificate_files', []);

                foreach ($deletedCTFiles as $file) {
                    if (Storage::exists('public/certificate_files/' . $file)) {
                        Storage::delete('public/certificate_files/' . $file);
                    }
                }
            }

            if ($request->hasFile('education_files', [])) {
                $eduFiles = $request->file('education_files');

                $eduFileUrls = [];

                foreach ($eduFiles as $file) {
                    $eduFileName = 'EDU-file' . uniqid() . '.' . $file->getClientOriginalExtension();
                    $eduFileUrl = Storage::putFileAs('public/education_files', $file, $eduFileName);
                    $eduFileUrls[] = $eduFileUrl;
                }

                $edu = ['education_files' => json_encode($eduFileUrls)];
                $data = array_merge($data, $edu);
            }

            if ($request->hasFile('cv_file')) {
                if (Storage::exists(json_decode($user->cv_file))) {
                    Storage::delete(json_decode($user->cv_file));
                }

                $cvFile = $request->file('cv_file');
                $cvFileName = 'CV-file' . uniqid() . '.' . $cvFile->getClientOriginalExtension();
                $cvFileUrl = Storage::putFileAs('public/cv_files', $cvFile, $cvFileName);
                $cv = ['cv_file' => json_encode($cvFileUrl)];
                $data = array_merge($data, $cv);
            }

            if ($request->hasFile('self_photo_file')) {
                if (Storage::exists(json_decode($user->self_photo_file))) {
                    Storage::delete(json_decode($user->self_photo_file));
                }

                $ppPhoto = $request->file('self_photo_file');
                $ppName = 'PP-' . uniqid() . '.' . $ppPhoto->getClientOriginalExtension();
                $ppPhotoUrl = Storage::putFileAs('public/profile_photos', $ppPhoto, $ppName);
                $pp = ['self_photo_file' => json_encode($ppPhotoUrl)];
                $data = array_merge($data, $pp);
            }

            if ($request->hasFile('certificate_files', [])) {
                $ctFiles = $request->file('certificate_files');

                $ctFileUrls = [];

                foreach ($ctFiles as $ctFileUrl) {
                    $ctFileName = 'Certificate-file' . uniqid() . '.' . $ctFileUrl->getClientOriginalExtension();
                    $ctFileUrl = Storage::putFileAs('public/certificate_files', $ctFileUrl, $ctFileName);
                    $ctFileUrls[] = $ctFileUrl;
                }

                $ct = ['certificate_files' => json_encode($ctFileUrls)];
                $data = array_merge($data, $ct);
            }

            $user->update($data);

            return $this->success(data: UserResource::make($user), message: "İstifadəçi uğurla yeniləndi");
        } else {
            return $this->error(message: "İstifadəçi tapılmadı", code: 404);
        }
    }

    public function show($user): JsonResponse
    {
        $user = User::query()->find($user);

        if ($user) {
            return $this->success(data: UserResource::make($user));
        } else {
            return $this->error(message: "İstifadəçi tapılmadı", code: 404);
        }
    }

    public function destroy($user): JsonResponse
    {
        $user = User::query()->find($user);

        if ($user) {

            if ($user->cv_file != null && $user->cv_file != '') {
                if (Storage::exists('public/cv_files/' . $user->cv_file)) {
                    Storage::delete('public/cv_files/' . $user->cv_file);
                }
            }

            if ($user->education_files != null && count(json_decode($user->education_files != null)) > 0) {
                $eduFiles = json_decode($user->education_files);

                foreach ($eduFiles as $file) {
                    if (Storage::exists('public/education_files/' . $file)) {
                        Storage::delete('public/education_files/' . $file);
                    }
                }
            }

            if ($user->certificate_files != null && count(json_decode($user->certificate_files != null)) > 0) {
                $ctFiles = json_decode($user->certificate_files);

                foreach ($ctFiles as $file) {
                    if (Storage::exists('public/certificate_files/' . $file)) {
                        Storage::delete('public/certificate_files/' . $file);
                    }
                }
            }

            if ($user->self_photo_file != null && $user->self_photo_file != '') {
                if (Storage::exists('public/profile_photos/' . $user->self_photo_file)) {
                    Storage::delete('public/profile_photos/' . $user->self_photo_file);
                }
            }

            $user->delete();

            return $this->success(message: "İstifadəçi uğurla silindi");
        } else {
            return $this->error(message: "İstifadəçi tapılmadı", code: 404);
        }
    }
}

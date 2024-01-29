<?php

namespace App\Http\Controllers\Api\V1\Users;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Auth\LoginRequest;
use App\Http\Requests\Api\V1\Auth\RegisterRequest;
use App\Models\User;
use App\Models\User\OtpCode;
use App\Traits\AuthTrait;
use App\Traits\HttpResponses;
use App\Traits\OTP;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    use HttpResponses, AuthTrait, OTP;

    public function register(RegisterRequest $request): JsonResponse
    {
        $data = $request->validated();

        $lowerCases = array_map('strtolower', $request->only('email', 'username'));
        $password = ['password' => Hash::make($request->password)];
        $accountStatus = ['account_status' => 'PENDING'];

        $data = array_merge($data, $accountStatus, $lowerCases, $password);

        $otpCode = $this->checkOTP('register', $request->phone, $request->otp_code);

        if (!$otpCode) {
            return $this->error(message: 'OTP kodu yanlışdır', code: 400);
        }

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

        User::query()->create($data);

        $otpCode->delete();

        return $this->success(message: 'İstifadəçi uğurla qeydiyyatdan keçdi');
    }

    public function login(LoginRequest $request): JsonResponse
    {
        if ($user = $this->getUser($request->phone, $request->password)) {
            $token = $user->createToken('loginToken')->plainTextToken;
            $user->update(['last_login_at' => now()]);

            return $this
                ->success(
                    data: [
                        'user' => $user,
                        'token' => $token
                    ],
                    message: "İstifadəçi uğurla giriş etdi",
                    code: 200
                );
        } else {
            return $this->error(message: 'İstifadəçi məlumatları yanlışdır', code: 401);
        }
    }

    public function logout(Request $request): JsonResponse
    {
        auth('user')->user()->currentAccessToken()->delete();

        return $this->success(message: "İstifadəçi uğurla çıxış etdi", code: 200);
    }

    public function resetPassword(Request $request): JsonResponse
    {
        $request->validate([
            'old_password' => ['required'],
            'password' => ['required', 'confirmed', 'min:8', 'max:32'],
        ]);

        $user = auth('user')->user();

        $otpCode = $this->checkOTP('reset_password', $user->phone, $request->otp_code);

        if (!$otpCode) {
            return $this->error(message: 'OTP kodu yanlışdır', code: 400);
        }

        if (Hash::check($request->old_password, $user->password)) {
            $user->password = Hash::make($request->password);
            $user->update();

            return $this->success(message: "İstifadəçi şifrəsi uğurla yeniləndi");
        } else {
            return $this->error(message: 'Köhnə şifrə düzgün deyil', code: 400);
        }
    }

    public function sendOTP(Request $request): JsonResponse
    {
        $otp = $this->generateOTP();

        $otpCode = OtpCode::query()->create([
            'phone' => $request->input('phone'),
            'code' => $otp,
            'type' => $request->input('type'),
            'expired_at' => now()->addSeconds(60)
        ]);

        return $this->success(data: $otpCode, message: "OTP kodu göndərildi");
    }
}

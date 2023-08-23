<?php

namespace App\Http\Controllers\Api\V1\Users;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Auth\LoginRequest;
use App\Traits\AuthTrait;
use App\Traits\HttpResponses;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    use HttpResponses;
    use AuthTrait;

    public function login(LoginRequest $request): JsonResponse
    {
        if ($user = $this->getUser($request->phone, $request->password)) {

            $token = $user->createToken('loginToken')->plainTextToken;

            return $this
                ->success(
                    data: [
                        'user' => $user,
                        'token' => $token
                    ],
                    message: "İstifadəçi uğurla giriş etdi"
                );
        } else {
            return $this->error(message: 'İstifadəçi məlumatları yanlışdır', code: 401);
        }
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return $this->success(data: '', message: "İstifadəçi uğurla çıxış etdi");
    }

    public function resetPassword(Request $request): JsonResponse
    {
        $request->validate([
            'old_password' => ['required'],
            'password' => ['required', 'confirmed', 'min:8', 'max:32'],
        ]);

        $user = $request->user();

        if (Hash::check($request->old_password, $user->password)) {
            $user->password = Hash::make($request->password);
            $user->update();

            return $this->success(data: '', message: "İstifadəçi şifrəsi uğurla yeniləndi");
        } else {
            return $this->error(message: 'Köhnə şifrə düzgün deyil', code: 400);
        }
    }
}

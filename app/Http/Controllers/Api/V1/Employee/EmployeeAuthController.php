<?php

namespace App\Http\Controllers\Api\V1\Employee;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Traits\HttpResponses;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Throwable;

class EmployeeAuthController extends Controller
{
    use HttpResponses;

    public function login(Request $request): Throwable|JsonResponse
    {
        $request->validate([
            'phone' => ['required', 'exists:employees,phone'],
            'password' => ['required', 'string']
        ]);

        $employee = Employee::query()->where('phone', $request->phone)->first();

        if (!$employee) {
            return $this->error(message: 'Əməkdaş məlumatları yanlışdır', code: 401);
        }

        $employeeStatus = Hash::check($request->password, $employee->password);

        if ($employeeStatus) {
            $token = $employee->createToken('loginToken')->plainTextToken;

            return $this
                ->success(
                    data: [
                        'employee' => $employee,
                        'token' => $token
                    ],
                    message: "Əməkdaş uğurla giriş etdi",
                    code: 200
                );
        } else {
            return $this->error(message: 'Əməkdaş məlumatları yanlışdır', code: 401);
        }
    }

    public function logout(Request $request): JsonResponse
    {
        $employee = auth('employee')->user();

        $employee->currentAccessToken()->delete();

        return $this->success(message: "Əməkdaş uğurla çıxış etdi", code: 200);
    }
}

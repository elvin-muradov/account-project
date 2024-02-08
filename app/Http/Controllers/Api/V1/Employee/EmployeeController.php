<?php

namespace App\Http\Controllers\Api\V1\Employee;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Employee\EmployeeStoreRequest;
use App\Http\Requests\Api\V1\Employee\EmployeeUpdateRequest;
use App\Http\Resources\Api\V1\Employee\EmployeeCollection;
use App\Http\Resources\Api\V1\Employee\EmployeeResource;
use App\Models\Company\Company;
use App\Models\Employee;
use App\Traits\HttpResponses;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class EmployeeController extends Controller
{
    use HttpResponses;

    public function index(Request $request): JsonResponse
    {
        $employees = Employee::query()->paginate($request->limit ?? 10);

        return $this->success(
            data: new EmployeeCollection($employees)
        );
    }

    public function store(EmployeeStoreRequest $request): JsonResponse
    {
        $data = $request->validated();
        $lowerCases = array_map('strtolower', $request->only('email'));
        $password = ['password' => Hash::make($request->password)];
        $data = array_merge($data, $lowerCases, $password);

        $employee = Employee::query()->create($data);

        return $this->success(
            data: EmployeeResource::make($employee),
            message: 'Əməkdaş uğurla yaradıldı',
            code: 201
        );
    }

    public function show($employee): JsonResponse
    {
        $employee = Employee::query()
            ->with('company')
            ->find($employee);

        if (!$employee) {
            return $this->error(message: 'Əməkdaş tapılmadı', code: 404);
        }

        return $this->success(
            data: EmployeeResource::make($employee)
        );
    }

    public function update(EmployeeUpdateRequest $request, $employee): JsonResponse
    {
        $data = $request->validated();
        $lowerCases = array_map('strtolower', $request->only('email'));
        $data = array_merge($data, $lowerCases);

        $employee = Employee::query()->find($employee);

        if ($request->has('password') && $request->password != null &&
            $request->password != '' && trim($request->password) != '') {

            $password = ['password' => Hash::make($request->password)];
            $data = array_merge($data, $lowerCases, $password);
        } else {
<<<<<<< HEAD
            $data = array_merge($data, $lowerCases, ['password ' => $employee->password]);
=======
            $data = array_merge($data, $lowerCases, ['password' => $employee->password]);
>>>>>>> development
        }


        if (!$employee) {
            return $this->error(message: 'Əməkdaş tapılmadı', code: 404);
        }

        $employee->update($data);

        return $this->success(
            data: EmployeeResource::make($employee),
            message: 'Əməkdaş uğurla yeniləndi'
        );
    }

    public function destroy($employee): JsonResponse
    {
        $employee = Employee::query()->find($employee);

        if (!$employee) {
            return $this->error(message: 'Əməkdaş tapılmadı', code: 404);
        }

        $employee->delete();

        return $this->success(
            message: 'Əməkdaş uğurla silindi'
        );
    }

    public function companyEmployees(Request $request, $company): JsonResponse
    {
        $employees = Employee::query()->where('company_id', $company)
            ->paginate($request->input('limit') ?? 10);

        return $this->success(data: new EmployeeCollection($employees));
    }
}

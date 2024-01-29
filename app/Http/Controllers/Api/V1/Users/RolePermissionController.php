<?php

namespace App\Http\Controllers\Api\V1\Users;

use App\Http\Controllers\Controller;
use App\Traits\HttpResponses;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class RolePermissionController extends Controller
{
    use HttpResponses;

    public function getAllRoles(): JsonResponse
    {
        $roles = Role::query()->get();

        return $this->success(data: $roles);
    }
}

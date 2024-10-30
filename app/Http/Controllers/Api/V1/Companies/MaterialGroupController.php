<?php

namespace App\Http\Controllers\Api\V1\Companies;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Companies\MaterialGroups\MaterialGroupStoreRequest;
use App\Http\Requests\Api\V1\Companies\MaterialGroups\MaterialGroupUpdateRequest;
use App\Http\Resources\Api\V1\Companies\MaterialGroups\MaterialGroupCollection;
use App\Http\Resources\Api\V1\Companies\MaterialGroups\MaterialGroupResource;
use App\Models\Company\Material\MaterialGroup;
use App\Traits\HttpResponses;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MaterialGroupController extends Controller
{
    use HttpResponses;

    public function index(Request $request): JsonResponse
    {
        $companyId = getHeaderCompanyId();

        if (!$companyId) {
            return $this->error(message: "Şirkət tapılmadı", code: 404);
        }

        $materialGroups = MaterialGroup::query()
            ->where('company_id', $companyId)
            ->with(['company'])
            ->paginate($request->input('limit') ?? 10);

        return $this->success(data: new MaterialGroupCollection($materialGroups));
    }

    public function show($materialGroup): JsonResponse
    {
        $companyId = getHeaderCompanyId();

        if (!$companyId) {
            return $this->error(message: "Şirkət tapılmadı", code: 404);
        }

        $materialGroup = MaterialGroup::query()
            ->where('company_id', $companyId)
            ->with(['company', 'materials'])
            ->find($materialGroup);

        if (!$materialGroup) {
            return $this->error(message: 'Material qrupu tapılmadı', code: 404);
        }

        return $this->success(data: MaterialGroupResource::make($materialGroup));
    }

    public function store(MaterialGroupStoreRequest $request): JsonResponse
    {
        $companyId = getHeaderCompanyId();

        if (!$companyId) {
            return $this->error(message: "Şirkət tapılmadı", code: 404);
        }

        $materialGroup = MaterialGroup::query()->create([
            'name' => $request->input('name'),
            'company_id' => $companyId
        ]);

        return $this->success(data: MaterialGroupResource::make($materialGroup),
            message: 'Material qrupu yaradıldı', code: 201);
    }

    public function update($materialGroup, MaterialGroupUpdateRequest $request): JsonResponse
    {
        $companyId = getHeaderCompanyId();

        if (!$companyId) {
            return $this->error(message: "Şirkət tapılmadı", code: 404);
        }

        $materialGroup = MaterialGroup::query()->where('company_id', $companyId)->find($materialGroup);

        if (!$materialGroup) {
            return $this->error(message: 'Material qrupu tapılmadı', code: 404);
        }

        $materialGroup->update([
            'name' => $request->input('name'),
            'company_id' => $companyId
        ]);

        return $this->success(data: MaterialGroupResource::make($materialGroup),
            message: 'Material qrupu yeniləndi', code: 200);
    }

    public function destroy($materialGroup): JsonResponse
    {
        $companyId = getHeaderCompanyId();

        if (!$companyId) {
            return $this->error(message: "Şirkət tapılmadı", code: 404);
        }

        $materialGroup = MaterialGroup::query()->where('company_id', $companyId)->find($materialGroup);

        if (!$materialGroup) {
            return $this->error(message: 'Material qrupu tapılmadı', code: 404);
        }

        $materialGroup->delete();

        return $this->success(message: 'Material qrupu silindi', code: 200);
    }
}

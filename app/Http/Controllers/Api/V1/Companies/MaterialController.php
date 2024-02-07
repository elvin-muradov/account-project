<?php

namespace App\Http\Controllers\Api\V1\Companies;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Companies\Materials\MaterialStoreRequest;
use App\Http\Requests\Api\V1\Companies\Materials\MaterialUpdateRequest;
use App\Http\Resources\Api\V1\Companies\Materials\MaterialCollection;
use App\Http\Resources\Api\V1\Companies\Materials\MaterialResource;
use App\Models\Company\Material\Material;
use App\Traits\HttpResponses;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MaterialController extends Controller
{
    use HttpResponses;

    public function index(Request $request): JsonResponse
    {
        $materials = Material::query()
            ->with(['materialGroup'])
            ->paginate($request->input('limit') ?? 10);

        return $this->success(data: new MaterialCollection($materials));
    }

    public function show($material): JsonResponse
    {
        $material = Material::query()
            ->with(['materialGroup', 'company', 'warehouse'])
            ->find($material);

        if (!$material) {
            return $this->error(message: 'Material tapılmadı', code: 404);
        }

        return $this->success(data: MaterialResource::make($material));
    }

    public function store(MaterialStoreRequest $request): JsonResponse
    {
        $material = Material::query()
            ->create([
                'name' => $request->input('name'),
                'code' => $request->input('code'),
                'description' => $request->input('description'),
                'company_id' => $request->input('company_id'),
                'material_group_id' => $request->input('material_group_id'),
                'warehouse_id' => $request->input('warehouse_id'),
            ]);

        return $this->success(data: MaterialResource::make($material),
            message: 'Material əlavə olundu', code: 201);
    }

    public function update(MaterialUpdateRequest $request, $material): JsonResponse
    {
        $material = Material::query()->find($material);

        if (!$material) {
            return $this->error(message: 'Material tapılmadı', code: 404);
        }

        $material->update([
            'name' => $request->input('name'),
            'code' => $request->input('code'),
            'description' => $request->input('description'),
            'company_id' => $request->input('company_id'),
            'material_group_id' => $request->input('material_group_id'),
            'warehouse_id' => $request->input('warehouse_id'),
        ]);

        return $this->success(data: MaterialResource::make($material),
            message: 'Material yeniləndi');
    }

    public function destroy($material): JsonResponse
    {
        $material = Material::query()->find($material);

        if (!$material) {
            return $this->error(message: 'Material tapılmadı', code: 404);
        }

        $material->delete();

        return $this->success(message: 'Material silindi');
    }
}

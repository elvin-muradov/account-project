<?php

namespace App\Http\Controllers\Api\V1\Companies;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Companies\Warehouses\WarehouseStoreRequest;
use App\Http\Requests\Api\V1\Companies\Warehouses\WarehouseUpdateRequest;
use App\Http\Resources\Api\V1\Companies\Warehouses\WarehouseCollection;
use App\Http\Resources\Api\V1\Companies\Warehouses\WarehouseResource;
use App\Models\Company\Warehouse;
use App\Traits\HttpResponses;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WarehouseController extends Controller
{
    use HttpResponses;

    public function index(Request $request): JsonResponse
    {
        $companyId = getHeaderCompanyId();

        if (!$companyId) {
            return $this->error(message: "Şirkət tapılmadı", code: 404);
        }

        $warehouses = Warehouse::query()
            ->where('company_id', $companyId)
            ->with(['company'])
            ->paginate($request->input('limit') ?? 10);

        return $this->success(data: new WarehouseCollection($warehouses));
    }

    public function show($warehouse): JsonResponse
    {
        $companyId = getHeaderCompanyId();

        if (!$companyId) {
            return $this->error(message: "Şirkət tapılmadı", code: 404);
        }

        $warehouse = Warehouse::query()
            ->where('company_id', $companyId)
            ->with(['company', 'materials'])
            ->find($warehouse);

        if (!$warehouse) {
            return $this->error(message: 'Anbar tapılmadı', code: 404);
        }

        return $this->success(data: WarehouseResource::make($warehouse));
    }

    public function store(WarehouseStoreRequest $request): JsonResponse
    {
        $companyId = getHeaderCompanyId();

        if (!$companyId) {
            return $this->error(message: "Şirkət tapılmadı", code: 404);
        }

        $warehouse = Warehouse::query()->create([
            'name' => $request->input('name'),
            'company_id' => $companyId
        ]);

        return $this->success(data: WarehouseResource::make($warehouse),
            message: 'Anbar yaradıldı', code: 201);
    }

    public function update($warehouse, WarehouseUpdateRequest $request): JsonResponse
    {
        $companyId = getHeaderCompanyId();

        if (!$companyId) {
            return $this->error(message: "Şirkət tapılmadı", code: 404);
        }

        $warehouse = Warehouse::query()->where('company_id', $companyId)->find($warehouse);

        if (!$warehouse) {
            return $this->error(message: 'Anbar tapılmadı', code: 404);
        }

        $warehouse->update([
            'name' => $request->input('name'),
            'company_id' => $companyId,
        ]);

        return $this->success(data: WarehouseResource::make($warehouse),
            message: 'Anbar yeniləndi', code: 200);
    }

    public function destroy($warehouse): JsonResponse
    {
        $companyId = getHeaderCompanyId();

        if (!$companyId) {
            return $this->error(message: "Şirkət tapılmadı", code: 404);
        }

        $warehouse = Warehouse::query()
            ->where('company_id', $companyId)
            ->find($warehouse);

        if (!$warehouse) {
            return $this->error(message: 'Anbar tapılmadı', code: 404);
        }

        $warehouse->delete();

        return $this->success(message: 'Anbar silindi', code: 200);
    }
}

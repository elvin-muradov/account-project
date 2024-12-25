<?php

namespace App\Http\Controllers\Api\V1\Companies;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\Companies\FixedAssetCategories\FixedAssetCategoryCollection;
use App\Http\Resources\Api\V1\Companies\FixedAssetCategories\FixedAssetCategoryResource;
use App\Models\Company\FixedAssetCategory;
use App\Traits\HttpResponses;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class FixedAssetCategoryController extends Controller
{
    use HttpResponses;

    public function index(Request $request): JsonResponse
    {
        $companyId = getHeaderCompanyId();

        if (!$companyId) {
            return $this->error(message: "Şirkət tapılmadı", code: 404);
        }

        $request->validate([
            'limit' => ['nullable', 'integer']
        ]);

        $fixedAssetCategories = FixedAssetCategory::query()
            ->where('company_id', $companyId)
            ->with(['company:id,company_name,company_short_name'])
            ->paginate($request->input('limit') ?? 10);

        return $this->success(data: new FixedAssetCategoryCollection($fixedAssetCategories));
    }

    public function store(Request $request): JsonResponse
    {
        $companyId = getHeaderCompanyId();

        if (!$companyId) {
            return $this->error(message: "Şirkət tapılmadı", code: 404);
        }

        $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('fixed_asset_categories', 'name')
                ->where('company_id', $companyId)],
        ]);

        $fixedAssetCategory = FixedAssetCategory::query()->create([
            'name' => $request->input('name'),
            'company_id' => $companyId
        ]);

        return $this->success(data: FixedAssetCategoryResource::make($fixedAssetCategory));
    }

    public function show($fixedAssetCategory): JsonResponse
    {
        $companyId = getHeaderCompanyId();

        if (!$companyId) {
            return $this->error(message: "Şirkət tapılmadı", code: 404);
        }

        $fixedAssetCategory = FixedAssetCategory::query()
            ->where('id', $fixedAssetCategory)
            ->where('company_id', $companyId)
            ->with(['company:id,company_name,company_short_name', 'fixedAssets'])
            ->first();

        if (!$fixedAssetCategory) {
            return $this->error(message: "Kateqoriya tapılmadı", code: 404);
        }

        return $this->success(data: FixedAssetCategoryResource::make($fixedAssetCategory));
    }

    public function update(Request $request, $fixedAssetCategory): JsonResponse
    {
        $companyId = getHeaderCompanyId();

        if (!$companyId) {
            return $this->error(message: "Şirkət tapılmadı", code: 404);
        }

        $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('fixed_asset_categories', 'name')
                ->where('company_id', $companyId)->ignore($fixedAssetCategory)],
        ]);

        $fixedAssetCategory = FixedAssetCategory::query()
            ->where('company_id', $companyId)
            ->find($fixedAssetCategory);

        if (!$fixedAssetCategory) {
            return $this->error(message: "Kateqoriya tapılmadı", code: 404);
        }

        $fixedAssetCategory->update([
            'name' => $request->input('name'),
            'company_id' => $companyId
        ]);

        return $this->success(data: FixedAssetCategoryResource::make($fixedAssetCategory));
    }

    public function destroy($fixedAssetCategory): JsonResponse
    {
        $companyId = getHeaderCompanyId();

        if (!$companyId) {
            return $this->error(message: "Şirkət tapılmadı", code: 404);
        }

        $fixedAssetCategory = FixedAssetCategory::query()
            ->where('company_id', $companyId)
            ->find($fixedAssetCategory);

        if (!$fixedAssetCategory) {
            return $this->error(message: "Kateqoriya tapılmadı", code: 404);
        }

        $fixedAssetCategory->delete();

        return $this->success(message: "Kateqoriya uğurla silindi");
    }
}

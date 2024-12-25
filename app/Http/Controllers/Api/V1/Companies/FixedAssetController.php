<?php

namespace App\Http\Controllers\Api\V1\Companies;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\Companies\FixedAssets\FixedAssetCollection;
use App\Http\Resources\Api\V1\Companies\FixedAssets\FixedAssetResource;
use App\Models\Company\FixedAsset;
use App\Traits\HttpResponses;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class FixedAssetController extends Controller
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

        $fixedAssets = FixedAsset::query()
            ->where('company_id', $companyId)
            ->with([
                'company:id,company_name,company_short_name',
                'fixedAssetCategory:id,name'
            ])
            ->paginate($request->input('limit') ?? 10);

        return $this->success(data: new FixedAssetCollection($fixedAssets));
    }

    public function store(Request $request): JsonResponse
    {
        $companyId = getHeaderCompanyId();

        if (!$companyId) {
            return $this->error(message: "Şirkət tapılmadı", code: 404);
        }

        $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('fixed_assets', 'name')
                ->where('company_id', $companyId)],
            'percentage' => ['required', 'numeric', 'min:0', 'max:100'],
            'fixed_asset_category_id' => ['required', Rule::exists('fixed_asset_categories', 'id')
                ->where('company_id', $companyId)],
            'start_of_using' => ['required', 'date'],
            'type' => ['required', 'in:vehicle,other'],
            'initial_value' => ['required', 'numeric'],
            'useful_life_years' => ['required', 'integer']
        ]);

        FixedAsset::query()->create([
            'name' => $request->input('name'),
            'percentage' => $request->input('percentage'),
            'fixed_asset_category_id' => $request->input('fixed_asset_category_id'),
            'initial_value' => $request->input('initial_value'),
            'useful_life_years' => $request->input('useful_life_years'),
            'start_of_using' => $request->input('start_of_using'),
            'end_of_using' => Carbon::createFromDate($request->input('start_of_using'))
                ->addYears($request->input('useful_life_years'))->format('Y-m-d'),
            'type' => $request->input('type'),
            'company_id' => $companyId
        ]);

        return $this->success(message: "Əsas vəsait uğurla əlavə olundu", code: 201);
    }

    public function update(Request $request, $fixedAsset): JsonResponse
    {
        $companyId = getHeaderCompanyId();

        if (!$companyId) {
            return $this->error(message: "Şirkət tapılmadı", code: 404);
        }

        $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('fixed_assets', 'name')
                ->where('company_id', $companyId)->ignore($fixedAsset)],
            'percentage' => ['required', 'numeric', 'between:0,100'],
            'fixed_asset_category_id' => ['required', Rule::exists('fixed_asset_categories', 'id')
                ->where('company_id', $companyId)],
            'type' => ['required', 'in:vehicle,other'],
            'start_of_using' => ['required', 'date'],
            'initial_value' => ['required', 'numeric'],
            'useful_life_years' => ['required', 'integer']
        ]);

        $fixedAsset = FixedAsset::query()->find($fixedAsset);

        if (!$fixedAsset) {
            return $this->error(message: "Əsas vəsait tapılmadı", code: 404);
        }

        $fixedAsset->update([
            'name' => $request->input('name'),
            'percentage' => $request->input('percentage'),
            'fixed_asset_category_id' => $request->input('fixed_asset_category_id'),
            'initial_value' => $request->input('initial_value'),
            'useful_life_years' => $request->input('useful_life_years'),
            'start_of_using' => $request->input('start_of_using'),
            'end_of_using' => Carbon::createFromDate($request->input('start_of_using'))
                ->addYears($request->input('useful_life_years'))->format('Y-m-d'),
            'type' => $request->input('type'),
            'company_id' => $companyId
        ]);

        return $this->success(message: "Əsas vəsait uğurla yeniləndi");
    }

    public function show($fixedAsset): JsonResponse
    {
        $companyId = getHeaderCompanyId();

        if (!$companyId) {
            return $this->error(message: "Şirkət tapılmadı", code: 404);
        }

        $fixedAsset = FixedAsset::query()
            ->where('company_id', '=', $companyId)
            ->with(['company:id,company_name,company_short_name',
                'fixedAssetCategory:id,name'])->find($fixedAsset);

        if (!$fixedAsset) {
            return $this->error(message: "Əsas vəsait tapılmadı", code: 404);
        }

        return $this->success(data: FixedAssetResource::make($fixedAsset));
    }

    public function destroy($fixedAsset): JsonResponse
    {
        $companyId = getHeaderCompanyId();

        if (!$companyId) {
            return $this->error(message: "Şirkət tapılmadı", code: 404);
        }

        $fixedAsset = FixedAsset::query()->find($fixedAsset);

        if (!$fixedAsset) {
            return $this->error(message: "Əsas vəsait tapılmadı", code: 404);
        }

        $fixedAsset->delete();

        return $this->success(message: "Əsas vəsait uğurla silindi");
    }
}

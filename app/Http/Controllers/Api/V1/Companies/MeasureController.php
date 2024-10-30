<?php

namespace App\Http\Controllers\Api\V1\Companies;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\Companies\Measures\MeasureCollection;
use App\Http\Resources\Api\V1\Companies\Measures\MeasureResource;
use App\Models\Measures\Measure;
use App\Traits\HttpResponses;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MeasureController extends Controller
{
    use HttpResponses;

    public function index(Request $request): JsonResponse
    {
        $companyId = getHeaderCompanyId();

        if (!$companyId) {
            return $this->error(message: "Şirkət tapılmadı", code: 404);
        }

        $measures = Measure::query()
            ->where('company_id', $companyId)
            ->paginate($request->input('limit') ?? 10);

        return $this->success(data: new MeasureCollection($measures));
    }

    public function show($measure): JsonResponse
    {
        $companyId = getHeaderCompanyId();

        if (!$companyId) {
            return $this->error(message: "Şirkət tapılmadı", code: 404);
        }

        $measure = Measure::query()->where('company_id', $companyId)->find($measure);

        if (!$measure) {
            return $this->error(message: "Ölçü vahidi tapılmadı", code: 404);
        }

        return $this->success(MeasureResource::make($measure));
    }

    public function store(Request $request): JsonResponse
    {
        $companyId = getHeaderCompanyId();

        if (!$companyId) {
            return $this->error(message: "Şirkət tapılmadı", code: 404);
        }

        $request->validate([
            'title' => ['required', 'string', Rule::unique('measures', 'title')
                ->where('company_id', $companyId)],
        ]);

        $measure = Measure::query()->create([
            'title' => $request->input('title'),
            'company_id' => $companyId
        ]);

        return $this->success(
            data: MeasureResource::make($measure),
            message: 'Ölçü vahidi ugurla əlavə olundu', code: 201);
    }

    public function update(Request $request, $measure): JsonResponse
    {
        $companyId = getHeaderCompanyId();

        if (!$companyId) {
            return $this->error(message: "Şirkət tapılmadı", code: 404);
        }

        $measure = Measure::query()->where('company_id', $companyId)->find($measure);

        if (!$measure) {
            return $this->error(message: "Ölçü vahidi tapılmadı", code: 404);
        }

        $request->validate([
            'title' => ['required', 'string',
                Rule::unique('measures', 'title')->where('company_id', $companyId)
                    ->ignore($measure)],
        ]);

        $measure->update([
            'title' => $request->input('title'),
            'company_id' => $companyId
        ]);

        return $this->success(
            data: MeasureResource::make($measure),
            message: 'Ölçü vahidi ugurla yeniləndi', code: 200);
    }

    public function destroy($measure): JsonResponse
    {
        $companyId = getHeaderCompanyId();

        if (!$companyId) {
            return $this->error(message: "Şirkət tapılmadı", code: 404);
        }

        $measure = Measure::query()->where('company_id', $companyId)->find($measure);

        if (!$measure) {
            return $this->error(message: "Ölçü vahidi tapılmadı", code: 404);
        }

        $measure->delete();

        return $this->success(message: 'Ölçü vahidi ugurla silindi', code: 200);
    }
}

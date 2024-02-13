<?php

namespace App\Http\Controllers\Api\V1\Companies;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\Companies\Measures\MeasureCollection;
use App\Http\Resources\Api\V1\Companies\Measures\MeasureResource;
use App\Models\Measures\Measure;
use App\Traits\HttpResponses;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MeasureController extends Controller
{
    use HttpResponses;

    public function index(Request $request): JsonResponse
    {
        $measures = Measure::query()
            ->paginate($request->input('limit') ?? 10);

        return $this->success(data: new MeasureCollection($measures));
    }

    public function show($measure): JsonResponse
    {
        $measure = Measure::query()->find($measure);

        if (!$measure) {
            return $this->error(message: "Ölçü vahidi tapılmadı", code: 404);
        }

        return $this->success(MeasureResource::make($measure));
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'title' => ['required', 'string', 'unique:measures,title'],
        ]);

        $measure = Measure::query()->create([
            'title' => $request->input('title'),
        ]);

        return $this->success(
            data: MeasureResource::make($measure),
            message: 'Ölçü vahidi ugurla əlavə olundu', code: 201);
    }

    public function update(Request $request, $measure): JsonResponse
    {
        $measure = Measure::query()->find($measure);

        if (!$measure) {
            return $this->error(message: "Ölçü vahidi tapılmadı", code: 404);
        }

        $request->validate([
            'title' => ['required', 'string', 'unique:measures,title,' . $measure->id],
        ]);

        $measure->update([
            'title' => $request->input('title'),
        ]);

        return $this->success(
            data: MeasureResource::make($measure),
            message: 'Ölçü vahidi ugurla yeniləndi', code: 200);
    }

    public function destroy($measure): JsonResponse
    {
        $measure = Measure::query()->find($measure);

        if (!$measure) {
            return $this->error(message: "Ölçü vahidi tapılmadı", code: 404);
        }

        $measure->delete();

        return $this->success(message: 'Ölçü vahidi ugurla silindi', code: 200);
    }
}

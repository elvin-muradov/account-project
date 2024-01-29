<?php

namespace App\Http\Controllers\Api\V1\Companies;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\Companies\ActivityCodes\ActivityCodeCollection;
use App\Http\Resources\Api\V1\Companies\ActivityCodes\ActivityCodeResource;
use App\Models\Company\ActivityCode;
use App\Traits\HttpResponses;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ActivityCodeController extends Controller
{
    use HttpResponses;

    public function index(Request $request): JsonResponse
    {
        $activityCodes = ActivityCode::query()
            ->with('company')
            ->paginate($request->limit ?? 10);

        return $this->success(data: new ActivityCodeCollection($activityCodes));
    }

    public function indexForCompany(Request $request): JsonResponse
    {
        $activityCodes = ActivityCode::query()
            ->where('company_id', $request->input('company_id'))
            ->paginate($request->limit ?? 10);

        return $this->success(data: new ActivityCodeCollection($activityCodes));
    }

    public function show($activityCode): JsonResponse
    {
        $activityCode = ActivityCode::query()->with('company')->find($activityCode);

        if (!$activityCode) {
            return $this->error(message: 'Fəaliyyət kodu tapılmadı', code: 404);
        }

        return $this->success(data: ActivityCodeResource::make($activityCode));
    }

    public function showForCompany(Request $request, $activityCode): JsonResponse
    {
        $request->validate([
            'company_id' => ['required', 'exists:companies,id'],
        ]);

        $activityCode = ActivityCode::query()
            ->where('company_id', $request->input('company_id'))
            ->find($activityCode);

        if (!$activityCode) {
            return $this->error(message: 'Fəaliyyət kodu tapılmadı', code: 404);
        }

        return $this->success(data: ActivityCodeResource::make($activityCode));
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'company_id' => ['required', 'exists:companies,id'],
            'activity_code' => ['required', 'integer', 'digits:7'],
        ]);

        $activityCode = ActivityCode::query()->create([
            'company_id' => $request->input('company_id'),
            'activity_code' => $request->input('activity_code'),
        ]);

        return $this->success(data: ActivityCodeResource::make($activityCode),
            message: "Fəaliyyət kodu yaradıldı", code: 201);
    }

    public function update(Request $request, $activityCode): JsonResponse
    {
        $activityCode = ActivityCode::query()->find($activityCode);

        if (!$activityCode) {
            return $this->error(message: 'Fəaliyyət kodu tapılmadı', code: 404);
        }

        $request->validate([
            'company_id' => ['required', 'exists:companies,id'],
            'activity_code' => ['required', 'integer', 'digits:7'],
        ]);

        $activityCode->update([
            'company_id' => $request->input('company_id'),
            'activity_code' => $request->input('activity_code'),
        ]);

        return $this->success(data: ActivityCodeResource::make($activityCode), message: 'Fəaliyyət kodu yeniləndi');
    }

    public function destroy($activityCode): JsonResponse
    {
        $activityCode = ActivityCode::query()->find($activityCode);

        if (!$activityCode) {
            return $this->error(message: 'Fəaliyyət kodu tapılmadı', code: 404);
        }

        $activityCode->delete();

        return $this->success(message: 'Fəaliyyət kodu silindi');
    }
}

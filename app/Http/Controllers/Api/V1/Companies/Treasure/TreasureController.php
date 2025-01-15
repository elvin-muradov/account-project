<?php

namespace App\Http\Controllers\Api\V1\Companies\Treasure;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\Companies\Treasure\TreasureCollection;
use App\Http\Resources\Api\V1\Companies\Treasure\TreasureResource;
use App\Models\Company\Treasure;
use App\Traits\HttpResponses;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TreasureController extends Controller
{
    use HttpResponses;

    public function index(Request $request): JsonResponse
    {
        $companyId = getHeaderCompanyId();

        if ($companyId) {
            $treasures = Treasure::query()
                ->where('company_id', $companyId)
                ->with('company:id,company_name,company_short_name')
                ->paginate($request->limit ?? 10);
        } else {
            return $this->error(message: "Şirkət tapılmadı", code: 404);
        }

        return $this->success(data: new TreasureCollection($treasures));
    }

    public function show($treasure): JsonResponse
    {
        $companyId = getHeaderCompanyId();

        if (!$companyId) {
            return $this->error(message: "Şirkət tapılmadı", code: 404);
        }

        $treasure = Treasure::query()
            ->where('company_id', $companyId)
            ->with('company:id,company_name,company_short_name')
            ->find($treasure);

        if (!$treasure) {
            return $this->error(message: 'Kassa tapılmadı', code: 404);
        }

        return $this->success(data: TreasureResource::make($treasure));
    }

    public function store(Request $request): JsonResponse
    {
        $companyId = getHeaderCompanyId();

        if (!$companyId) {
            return $this->error(message: "Şirkət tapılmadı", code: 404);
        }


        $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('treasures', 'name')
                ->where('company_id', $companyId)],
            'balance' => ['required', 'numeric', 'min:0'],
        ]);

        $treasure = Treasure::query()->create([
            'company_id' => $companyId,
            'name' => $request->input('name'),
            'balance' => $request->input('balance'),
        ]);

        return $this->success(data: TreasureResource::make($treasure),
            message: "Kassa yaradıldı", code: 201);
    }

    public function update(Request $request, $treasure): JsonResponse
    {
        $companyId = getHeaderCompanyId();

        if (!$companyId) {
            return $this->error(message: "Şirkət tapılmadı", code: 404);
        }

        $treasure = Treasure::query()
            ->where('company_id', $companyId)
            ->find($treasure);

        if (!$treasure) {
            return $this->error(message: 'Kassa tapılmadı', code: 404);
        }

        $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('treasures', 'name')
                ->where('company_id', $companyId)->ignore($treasure)],
            'balance' => ['required', 'numeric', 'min:0'],
        ]);

        $treasure->update([
            'company_id' => $companyId,
            'name' => $request->input('name'),
            'balance' => $request->input('balance', $treasure->balance),
        ]);

        return $this->success(data: TreasureResource::make($treasure), message: 'Kassa yeniləndi');
    }

    public function destroy($treasure): JsonResponse
    {
        $companyId = getHeaderCompanyId();

        if (!$companyId) {
            return $this->error(message: "Şirkət tapılmadı", code: 404);
        }

        $treasure = Treasure::query()
            ->where('company_id', $companyId)
            ->find($treasure);

        if (!$treasure) {
            return $this->error(message: 'Kassa tapılmadı', code: 404);
        }

        $treasure->delete();

        return $this->success(message: 'Kassa silindi');
    }
}

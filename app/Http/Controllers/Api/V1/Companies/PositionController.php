<?php

namespace App\Http\Controllers\Api\V1\Companies;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\Companies\Positions\PositionCollection;
use App\Http\Resources\Api\V1\Companies\Positions\PositionResource;
use App\Models\Company\Company;
use App\Models\Company\Position;
use App\Traits\HttpResponses;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PositionController extends Controller
{
    use HttpResponses;

    public function index(Request $request): JsonResponse
    {
        $positions = Position::query()
            ->with('company')
            ->paginate($request->input('limit') ?? 10);

        return $this->success(data: new PositionCollection($positions));
    }

    public function show($positions): JsonResponse
    {
        $position = Position::query()
            ->with('company')
            ->find($positions);

        if (!$position) {
            return $this->error(message: 'Vəzifə tapılmadı', code: 404);
        }

        return $this->success(data: PositionResource::make($position));
    }

    public function showPositionsByCompany(Request $request): JsonResponse
    {
        $company = Company::query()->with('positions')
            ->find($request->input('company_id'));

        if (!$company) {
            return $this->error(message: "Şirkət tapılmadı", code: 404);
        }

        return $this->success(data: new PositionCollection($company->positions));
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('positions', 'name')
                ->where('company_id', $request->input('company_id'))],
            'company_id' => ['required', 'integer', 'exists:companies,id'],
        ]);

        $position = Position::query()->create([
            'name' => $request->input('name'),
            'company_id' => $request->input('company_id'),
        ]);

        return $this->success(data: PositionResource::make($position), message: 'Vəzifə əlavə edildi', code: 201);
    }

    public function update(Request $request, $position): JsonResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('positions', 'name')
                ->where('company_id', $request->input('company_id'))
                ->ignore($position)],
            'company_id' => ['required', 'integer', 'exists:companies,id'],
        ]);

        $position = Position::query()->find($position);

        if (!$position) {
            return $this->error(message: 'Vəzifə tapılmadı', code: 404);
        }

        $position->update([
            'name' => $request->input('name'),
            'company_id' => $request->input('company_id'),
        ]);

        return $this->success(data: PositionResource::make($position), message: 'Vəzifə uğurla yeniləndi');
    }

    public function destroy($position): JsonResponse
    {
        $position = Position::query()->find($position);

        if (!$position) {
            return $this->error(message: 'Vəzifə tapılmadı', code: 404);
        }

        $position->delete();

        return $this->success(message: 'Vəzifə uğurla silindi');
    }
}

<?php

namespace App\Http\Controllers\Api\V1\Companies;

use App\Enums\UserTypesEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Companies\RentalContract\RentalContractStoreRequest;
use App\Http\Requests\Api\V1\Companies\RentalContract\RentalContractUpdateRequest;
use App\Http\Resources\Api\V1\Companies\RentalContracts\RentalContractCollection;
use App\Http\Resources\Api\V1\Companies\RentalContracts\RentalContractResource;
use App\Models\Company\RentalContract;
use App\Traits\HttpResponses;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RentalContractController extends Controller
{
    use HttpResponses;

    public function indexAllRentalContracts(Request $request): JsonResponse
    {
        $companyId = getHeaderCompanyId();

        if (!$companyId) {
            return $this->error(message: "Şirkət tapılmadı", code: 404);
        }

        $rentalContracts = RentalContract::query()
            ->where('company_id', $companyId)
            ->with(['company'])
            ->paginate($request->limit ?? 10);

        return $this->success(data: new RentalContractCollection($rentalContracts));
    }

    public function indexShopRentalContracts(Request $request): JsonResponse
    {
        $companyId = getHeaderCompanyId();

        if (!$companyId) {
            return $this->error(message: "Şirkət tapılmadı", code: 404);
        }

        $shopRentalContracts = RentalContract::query()
            ->where('company_id', $companyId)
            ->where('type', 'SHOP')
            ->with(['company'])
            ->paginate($request->limit ?? 10);

        return $this->success(data: new RentalContractCollection($shopRentalContracts));
    }

    public function indexWarehouseRentalContracts(Request $request): JsonResponse
    {
        $companyId = getHeaderCompanyId();

        if (!$companyId) {
            return $this->error(message: "Şirkət tapılmadı", code: 404);
        }

        $warehouseRentalContracts = RentalContract::query()
            ->where('company_id', $companyId)
            ->where('type', 'WAREHOUSE')
            ->with(['company'])
            ->paginate($request->limit ?? 10);

        return $this->success(data: new RentalContractCollection($warehouseRentalContracts));
    }

    public function indexVehicleRentalContracts(Request $request): JsonResponse
    {
        $companyId = getHeaderCompanyId();

        if (!$companyId) {
            return $this->error(message: "Şirkət tapılmadı", code: 404);
        }

        $vehicleRentalContracts = RentalContract::query()
            ->where('company_id', $companyId)
            ->where('type', 'VEHICLE')
            ->with(['company'])
            ->paginate($request->limit ?? 10);

        return $this->success(data: new RentalContractCollection($vehicleRentalContracts));
    }

    public function store(RentalContractStoreRequest $request): JsonResponse
    {
        $companyId = getHeaderCompanyId();

        if (!$companyId) {
            return $this->error(message: "Şirkət tapılmadı", code: 404);
        }

        $data = $request->validated();

        $data = array_merge($data, ['company_id' => $companyId]);

        if ($request->hasFile('contract_files')) {
            $contractFiles = $request->file('contract_files');
            $data = array_merge($data, ['contract_files' => returnFilesArray($contractFiles, 'contract_files')]);
        }

        $data = array_merge($data, ['creator_id' => auth()->id()]);

        if ((bool)$request->input('is_vat')) {
            $rentalPriceWithVAT = $request->input('rental_price') + ($request->input('rental_price') * 0.18);
            $data = array_merge($data, ['rental_price_with_vat' => $rentalPriceWithVAT]);
        }

        $rentalContract = RentalContract::query()->create($data);

        return $this->success(data: RentalContractResource::make($rentalContract),
            message: "İcarə müqəviləsi uğurla əlavə olundu", code: 201);
    }

    public function update(RentalContractUpdateRequest $request, $rentalContract): JsonResponse
    {
        $companyId = getHeaderCompanyId();

        if (!$companyId) {
            return $this->error(message: "Şirkət tapılmadı", code: 404);
        }

        $data = $request->validated();

        $rentalContract = RentalContract::query()
            ->where('company_id', $companyId)
            ->find($rentalContract);

        if (!$rentalContract) {
            return $this->error(message: "İcarə müqaviləsi tapılmadı", code: 404);
        }

        $data = array_merge($data, ['company_id' => $companyId]);

        if ($request->has('delete_contract_files') && $request->delete_contract_files != null) {
            $deletedRentalContractFiles = $request->input('delete_contract_files');
            $rentalContractFiles = $rentalContract->contract_files ?? [];
            $deletedFiles = deleteFiles($deletedRentalContractFiles, $rentalContractFiles, true);
            if (is_array($deletedFiles)) {
                $rentalContract->contract_files = array_values($deletedFiles);
            } else {
                return $this->error(
                    message: "Ən az bir faylın qalması vacibdir", code: 400
                );
            }
        }

        if ($request->hasFile('contract_files')) {
            $rentalContractFiles = $request->file('contract_files');
            $rentalContractFilesArr = $rentalContract->contract_files;
            $updatedFiles = returnFilesArray($rentalContractFiles, 'contract_files');
            $data = array_merge($data, ['contract_files' => array_merge($rentalContractFilesArr, $updatedFiles)]);
        }

        if ((bool)$request->input('is_vat')) {
            $rentalPriceWithVAT = $request->input('rental_price') + ($request->input('rental_price') * 0.18);
            $data = array_merge($data, ['rental_price_with_vat' => $rentalPriceWithVAT]);
        }

        $rentalContract->update($data);

        return $this->success(data: RentalContractResource::make($rentalContract),
            message: "İcarə müqaviləsi uğurla yeniləndi");
    }

    public function show($rentalContract): JsonResponse
    {
        $companyId = getHeaderCompanyId();

        if (!$companyId) {
            return $this->error(message: "Şirkət tapılmadı", code: 404);
        }

        $rentalContract = RentalContract::query()
            ->where('company_id', $companyId)
            ->with(['company'])
            ->find($rentalContract);

        if (!$rentalContract) {
            return $this->error(message: "İcarə müqaviləsi tapılmadı", code: 404);
        }

        return $this->success(data: RentalContractResource::make($rentalContract));
    }

    public function destroy($rentalContract): JsonResponse
    {
        $companyId = getHeaderCompanyId();

        if (!$companyId) {
            return $this->error(message: "Şirkət tapılmadı", code: 404);
        }

        $rentalContract = RentalContract::query()
            ->where('company_id', $companyId)
            ->find($rentalContract);

        if (!$rentalContract) {
            return $this->error(message: "İcarə müqaviləsi tapılmadı", code: 404);
        }

        if ($rentalContract->contract_files != null && count($rentalContract->contract_files) > 0) {
            checkFilesAndDeleteFromStorage($rentalContract->contract_files);
        }

        $rentalContract->delete();

        return $this->success(message: "İcarə müqaviləsi uğurla silindi");
    }
}

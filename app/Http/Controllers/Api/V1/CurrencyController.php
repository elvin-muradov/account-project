<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\Currency\CurrencyCollection;
use App\Models\Currency;
use App\Traits\HttpResponses;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CurrencyController extends Controller
{
    use HttpResponses;


    public function index(Request $request): JsonResponse
    {
        $currencies = Currency::query()
            ->paginate($request->input('limit') ?? 10);

        return $this->success(data: new CurrencyCollection($currencies));
    }
}

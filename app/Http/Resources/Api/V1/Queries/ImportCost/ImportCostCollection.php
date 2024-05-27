<?php

namespace App\Http\Resources\Api\V1\Queries\ImportCost;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ImportCostCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'data' => $this->collection,
            'currentPage' => $this->currentPage(),
            'perPage' => $this->perPage(),
            'total' => $this->total(),
        ];
    }
}

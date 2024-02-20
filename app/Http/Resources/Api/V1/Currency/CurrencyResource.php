<?php

namespace App\Http\Resources\Api\V1\Currency;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CurrencyResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'title' => $this->title,
            'short_title' => $this->short_title,
            'rate' => $this->rate,
            'symbol' => $this->symbol
        ];
    }
}

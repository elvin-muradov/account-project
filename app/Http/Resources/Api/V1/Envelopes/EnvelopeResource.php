<?php

namespace App\Http\Resources\Api\V1\Envelopes;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EnvelopeResource extends JsonResource
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
            'type' => $this->type,
            'code' => $this->code,
            'from_company_id' => $this->from_company_id,
            'to_company_id' => $this->to_company_id,
            'creator_id' => $this->creator_id,
            'fromCompany' => $this->whenLoaded('fromCompany'),
            'toCompany' => $this->whenLoaded('toCompany'),
            'from_company_name' => $this->from_company_name,
            'to_company_name' => $this->to_company_name,
            'creator' => $this->whenLoaded('creator'),
            'envelopes' => $this->envelopes,
            'sent_at' => Carbon::parse($this->sent_at)->format('d.m.Y'),
        ];
    }
}

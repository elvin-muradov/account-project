<?php

namespace App\Http\Resources\Api\V1\Envelopes;

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
            'from_company_id' => $this->from_company_id,
            'to_company_id' => $this->to_company_id,
            'sender_id' => $this->sender_id,
            'fromCompany' => $this->whenLoaded('fromCompany'),
            'toCompany' => $this->whenLoaded('toCompany'),
            'sender' => $this->whenLoaded('sender'),
            'envelopes' => $this->envelopes,
            'sent_at' => $this->sent_at
        ];
    }
}

<?php

namespace App\Http\Resources\Api\V1\Queries;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ImportQueryResource extends JsonResource
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
            'company_id' => $this->company_id,
            'company' => $this->whenLoaded('company') ? [
                'id' => $this->company?->id,
                'company_name' => $this->company?->company_name,
            ] : null,
            'query_number' => $this->query_number,
            'customs_barcode' => $this->customs_barcode,
            'shipping_from' => $this->shipping_from,
            'seller_company_name' => $this->seller_company_name,
            'delivery_date' => $this->delivery_date,
            'customs_date' => $this->customs_date,
            'invoice_value' => $this->invoice_value,
            'customs_value' => $this->customs_value,
            'statistic_value' => $this->statistic_value,
            'net_weight' => $this->net_weight,
            'currency_id' => $this->currency_id,
            'currency' => $this->whenLoaded('currency'),
            'transport_type' => $this->transport_type,
            'payment_status' => $this->payment_status,
            'customs_transaction_fee' => $this->customs_transaction_fee,
            'customs_transaction_fee_24_hours' => $this->customs_transaction_fee_24_hours,
            'import_fee' => $this->import_fee,
            'vat' => $this->vat,
            'electronic_customs_fee' => $this->electronic_customs_fee,
            'vat_for_electronic_customs_fee' => $this->vat_for_electronic_customs_fee,
            'created_at' => $this->created_at,
            'importQueryDetails' => $this->whenLoaded('importQueryDetails'),
        ];
    }
}

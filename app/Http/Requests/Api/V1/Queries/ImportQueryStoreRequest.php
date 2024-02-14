<?php

namespace App\Http\Requests\Api\V1\Queries;

use App\Enums\ImportPaymentStatuses;
use App\Enums\TransportTypes;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ImportQueryStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'company_id' => ['required', 'integer', 'exists:companies,id'], // Şirkət adı
            'query_number' => ['required', 'string', 'max:255', 'unique:import_queries,query_number'], // Sorğu №
            'customs_barcode' => ['required', 'string', 'max:255', 'unique:import_queries,customs_barcode'], // Sorğu №
            'seller_company_name' => ['required', 'string', 'max:255'], // Satış sirkəti adı
            'currency_id' => ['required', 'integer', 'exists:currencies,id'], // Valyuta
            'shipping_from' => ['required', 'string', 'max:255'],
            'delivery_date' => ['required', 'date'], // Təhvil tarixi
            'customs_date' => ['required', 'date'], // Gömrükə gəlmə tarixi
            'transport_type' => ['required', 'string', 'in:' . TransportTypes::toString()], // Transport
            'payment_status' => ['required', 'string', 'in:' . ImportPaymentStatuses::toString()],
            'invoice_value' => ['required', 'numeric'], // İnvoys dəyəri
            'customs_value' => ['required', 'numeric'], // Gömrük dəyəri
            'statistic_value' => ['required', 'numeric'], // Statistik dəyəri
            'customs_transaction_fee' => ['required', 'numeric'], // Gömrük əməliyyat haqqı 2
            'customs_transaction_fee_24_hours' => ['required', 'numeric'], // Gömrük əməliyyat haqqı (24 saat) - 19
            'import_fee' => ['required', 'numeric'], // İdxal rüsumu 15% - 20
            'vat' => ['required', 'numeric'], // Vat 18% - 32
            'electronic_customs_fee' => ['required', 'numeric'], // Elektronik gömrük haqqı - 75
            'vat_for_electronic_customs_fee' => ['required', 'numeric'], // Elektronik gömrük ƏDV - 85
            'net_weight' => ['required', 'numeric'], // NET çəki
            'import_query_details.*' => ['required', 'array'],
            'import_query_details.*.material_title_local' => ['required', 'string', 'max:255'],
            'import_query_details.*.material_barcode' => ['required', 'string', 'max:255'],
            'import_query_details.*.material_title_az' => ['required', 'string', 'max:255'],
            'import_query_details.*.measure' => ['required', 'string', 'max:255'],
            'import_query_details.*.quantity' => ['required', 'numeric'],
            'import_query_details.*.price_per_unit_of_measure' => ['required', 'numeric'],
            'import_query_details.*.subtotal_amount' => ['required', 'numeric'],
        ];
    }
}

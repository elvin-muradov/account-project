<?php

namespace App\Models\Queries;

use App\Models\Company\Company;
use App\Models\Currency;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ImportQuery extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'company_id' => 'integer',
        'currency_id' => 'integer',
        'net_weight' => 'float',
        'invoice_value' => 'float',
        'customs_value' => 'float',
        'statistic_value' => 'float',
        'customs_transaction_fee' => 'float',
        'customs_transaction_fee_24_hours' => 'float',
        'import_fee' => 'float',
        'vat' => 'float',
        'electronic_customs_fee' => 'float',
        'vat_for_electronic_customs_fee' => 'float'
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class, 'currency_id');
    }

    public function importQueryDetails(): HasMany
    {
        return $this->hasMany(ImportQueryDetail::class, 'import_query_id');
    }
}

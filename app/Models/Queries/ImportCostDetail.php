<?php

namespace App\Models\Queries;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ImportCostDetail extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'import_cost_id' => 'integer',
        'ratio' => 'float',
        'short_import_duty' => 'float',
        'customs_short_and_import_duty' => 'float',
        'other_expenses' => 'float',
        'customs_collection' => 'float',
        'transport_expenses' => 'float',
        'import_fee_and_other_expenses' => 'float',
        'vat' => 'float',
        'subtotal_amount_azn' => 'float',
        'price_per_unit_of_measure_azn' => 'float',
        'quantity' => 'float',
    ];

    public function importCost(): BelongsTo
    {
        return $this->belongsTo(ImportCost::class, 'import_cost_id');
    }

    public function importQueryDetail(): BelongsTo
    {
        return $this->belongsTo(ImportQueryDetail::class, 'import_query_detail_id');
    }
}

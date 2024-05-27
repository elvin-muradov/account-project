<?php

namespace App\Models\Queries;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ImportCost extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'import_query_id' => 'integer',
        'total_ratio' => 'float',
        'total_amount' => 'float',
        'total_short_import_duty' => 'float',
        'total_customs_short_and_import_duty' => 'float',
        'total_other_expenses' => 'float',
        'total_customs_collection' => 'float',
        'total_transport_expenses' => 'float',
        'total_import_fee_and_other_expenses' => 'float',
        'total_vat' => 'float',
        'total_amount_azn' => 'float'
    ];

    public function importQuery(): BelongsTo
    {
        return $this->belongsTo(ImportQuery::class, 'import_query_id');
    }

    public function importCostDetails(): HasMany
    {
        return $this->hasMany(ImportCostDetail::class, 'import_cost_id');
    }
}

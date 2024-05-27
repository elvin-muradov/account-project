<?php

namespace App\Models\Queries;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ImportQueryDetail extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'quantity' => 'float',
        'subtotal_amount' => 'float',
        'price_per_unit_of_measure' => 'float',
    ];

    public function importQuery(): BelongsTo
    {
        return $this->belongsTo(ImportQuery::class, 'import_query_id');
    }

    public function importCostDetail(): HasOne
    {
        return $this->hasOne(ImportCostDetail::class, 'import_query_detail_id');
    }
}

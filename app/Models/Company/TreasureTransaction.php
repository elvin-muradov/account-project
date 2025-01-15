<?php

namespace App\Models\Company;

use App\Models\Currency;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TreasureTransaction extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'amount' => 'float',
        'currency_id' => 'integer',
        'company_id' => 'integer',
        'treasure_id' => 'integer',
        'treasure_transaction_id' => 'integer',
        'is_disabled' => 'boolean',
        'treasure_balance' => 'float'
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class, 'currency_id');
    }

    public function treasure(): BelongsTo
    {
        return $this->belongsTo(Treasure::class, 'treasure_id');
    }

    public function treasure_transaction(): BelongsTo
    {
        return $this->belongsTo(TreasureTransaction::class, 'treasure_transaction_id');
    }
}

<?php

namespace App\Models\Orders;

use App\Models\Company\Company;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PregnantOrder extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'generated_file' => 'array',
        'company_id' => 'integer'
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id');
    }
}

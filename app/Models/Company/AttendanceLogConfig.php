<?php

namespace App\Models\Company;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttendanceLogConfig extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'company_id' => 'integer',
        'config' => 'array',
        'year' => 'integer',
        'month' => 'integer'
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id');
    }
}

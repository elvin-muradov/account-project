<?php

namespace App\Models\Company;

use Carbon\Carbon;
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
        'month' => 'integer',
        'log_date' => 'date',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function getLogDateAttribute($value): string
    {
        return Carbon::parse($value)->format('Y-m-d');
    }
}

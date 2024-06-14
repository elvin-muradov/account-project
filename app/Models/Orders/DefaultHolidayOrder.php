<?php

namespace App\Models\Orders;

use App\Models\Company\Company;
use App\Models\Employee;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DefaultHolidayOrder extends Model
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

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }
}

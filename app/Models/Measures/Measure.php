<?php

namespace App\Models\Measures;

use App\Models\Company\Company;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Measure extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'company_id',
    ];


    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

}

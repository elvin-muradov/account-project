<?php

namespace App\Models\Envelopes;

use App\Models\Company\Company;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Envelope extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'from_company_id' => 'integer',
        'to_company_id' => 'integer',
        'creator_id' => 'integer',
        'from_company_name' => 'string',
        'to_company_name' => 'string',
        'envelopes' => 'array'
    ];

    public function fromCompany(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'from_company_id');
    }

    public function toCompany(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'to_company_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }
}

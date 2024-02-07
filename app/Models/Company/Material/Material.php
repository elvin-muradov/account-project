<?php

namespace App\Models\Company\Material;

use App\Models\Company\Company;
use App\Models\Company\Warehouse;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Material extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'company_id' => 'integer',
        'material_group_id' => 'integer',
        'warehouse_id' => 'integer',
    ];

    public function materialGroup(): BelongsTo
    {
        return $this->belongsTo(MaterialGroup::class, 'material_group_id');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id');
    }


    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_id');
    }
}

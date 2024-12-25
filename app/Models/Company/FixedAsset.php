<?php

namespace App\Models\Company;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FixedAsset extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'name' => 'string',
        'initial_value' => 'float',
        'useful_life_years' => 'integer',
        'percentage' => 'float'
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function fixedAssetCategory(): BelongsTo
    {
        return $this->belongsTo(FixedAssetCategory::class, 'fixed_asset_category_id');
    }

    public function getResidualValue($selectedYear)
    {
        return $this->calculateAmortization($selectedYear);
    }

    private function calculateAmortization($selectedYear)
    {
        $initialValue = $this->initial_value;
        $percentage = $this->percentage;
        $usefulLifeYears = $this->useful_life_years;
        $startOfUsingDate = Carbon::parse($this->start_of_using)->format('Y-m-d');
        $startYear = Carbon::parse($startOfUsingDate)->format('Y');
        $endYear = Carbon::parse($startOfUsingDate)->addYears($usefulLifeYears)->format('Y');
        $yearlyAmortization = $initialValue * ($percentage / 100);
        $residualValue = 0;
        $years = [];

        $count = 1;
        foreach (range($startYear, $endYear) as $year) {
            $years[$count] = $year;
            $count++;
        }

        if (in_array($selectedYear, $years)) {
            $residualValue = $initialValue - ($yearlyAmortization * ($selectedYear - $startYear));
        }

        return $residualValue;
    }
}

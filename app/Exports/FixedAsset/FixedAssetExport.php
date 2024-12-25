<?php

namespace App\Exports\FixedAsset;

use App\Models\Company\FixedAsset;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class FixedAssetExport implements FromView
{
    private mixed $req;

    public function __construct($req)
    {
        $this->req = $req;
    }

    public function view(): View
    {
        $fixedAssets = FixedAsset::query()
            ->where('company_id', '=', $this->req['company_id'])
            ->whereDate('start_of_using', '<', $this->req['year'] . '-01-01')
            ->whereDate('end_of_using', '>', $this->req['year'] . '-01-01')
            ->get();

        return view('exports.fixed_assets.fixed_asset_export', [
            'fixedAssets' => $fixedAssets,
            'selectedYear' => $this->req['year']
        ]);
    }
}

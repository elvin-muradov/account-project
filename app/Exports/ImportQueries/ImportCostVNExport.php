<?php

namespace App\Exports\ImportQueries;

use App\Models\Queries\ImportCost;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class ImportCostVNExport implements FromView
{
    public mixed $selectedIds;

    public function __construct($selectedIds)
    {
        $this->selectedIds = $selectedIds;
    }

    public function view(): View
    {
        $importCosts = ImportCost::query()
            ->with(['importCostDetails'])
            ->get();

        if (is_array($this->selectedIds) && !empty($this->selectedIds)) {
            $importCosts = ImportCost::query()
                ->whereIn('id', $this->selectedIds)
                ->with(['importCostDetails', 'importQuery'])
                ->get();
        }

        return view('exports.import_costs.import_cost_vn_export', [
            'importCosts' => $importCosts
        ]);
    }
}

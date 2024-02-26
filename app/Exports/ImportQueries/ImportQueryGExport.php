<?php

namespace App\Exports\ImportQueries;

use App\Models\Queries\ImportQuery;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromView;

class ImportQueryGExport implements FromView
{
    public mixed $selectedIds;

    public function __construct($selectedIds)
    {
        $this->selectedIds = $selectedIds;
    }

    public function view(): View
    {
        $importQueries = ImportQuery::query()
            ->with(['importQueryDetails', 'company', 'currency'])
            ->get();

        if (is_array($this->selectedIds) && !empty($this->selectedIds)) {
            $importQueries = ImportQuery::query()
                ->whereIn('id', $this->selectedIds)
                ->with(['importQueryDetails', 'company', 'currency'])
                ->get();
        }

        return view('exports.import_queries.import_query_export_g', [
            'importQueries' => $importQueries
        ]);
    }
}

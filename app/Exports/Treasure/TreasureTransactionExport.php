<?php

namespace App\Exports\Treasure;

use App\Models\Company\TreasureTransaction;
use App\Traits\HttpResponses;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class TreasureTransactionExport implements FromView
{
    use HttpResponses;

    private mixed $req;

    public function __construct($req)
    {
        $this->req = $req;
    }

    public function view(): View
    {
        //$companyId = getHeaderCompanyId();
        $daysOfMonth = Carbon::create($this->req['year'], $this->req['month'], 01)->daysInMonth;

        //dd($this->req);
        $transactions = TreasureTransaction::query()
            ->with([
                'company:id,company_name,company_short_name,tax_id_number',
                'company.director:id,name,surname',
                'treasure:id,name,balance',
                'treasure_transaction:id,destination,invoice_number,transaction_date'
            ])
            ->where('company_id', '=', 1)
            ->whereBetween('transaction_date', [
                $this->req['year'] . '-' . $this->req['month'] . '-01',
                $this->req['year'] . '-' . $this->req['month'] . '-' . $daysOfMonth
            ])
            ->get();

        return view('exports.treasure_transactions.treasure_transaction_export', [
            'transactions' => $transactions,
            'req' => $this->req
        ]);
    }
}

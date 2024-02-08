<?php

namespace App\Console\Commands;

use App\Models\Currency;
use Carbon\Carbon;
use Illuminate\Console\Command;

class UpdateCurrencyRate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pull:currency-rates';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get currency rates from CBAR';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $today = Carbon::parse(Carbon::now())->format('d.m.Y');
        $cbarRates = getCbaRates($today);

        foreach ($cbarRates as $rate) {
            Currency::query()->updateOrCreate(
                [
                    'code' => $rate['code'],
                ],
                [
                    'short_title' => $rate['code'],
                    'title' => $rate['name'],
                    'symbol' => $rate['symbol'],
                    'rate' => floatval(number_format($rate['rate'], 3, '.', ''))
                ]
            );
        }
    }
}

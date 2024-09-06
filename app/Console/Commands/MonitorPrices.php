<?php

namespace App\Console\Commands;

use App\Jobs\CheckAdPriceJob;
use Illuminate\Console\Command;
use App\Models\Ad;

class MonitorPrices extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'monitor:prices';

    /**
     * The console command description.
     */
    protected $description = 'Monitor OLX ad prices and notify subscribers on changes.';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $ads = Ad::all();

        foreach ($ads as $ad) {
            CheckAdPriceJob::dispatch($ad);
        }
    }
}

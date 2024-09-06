<?php

namespace Tests\Unit;

use App\Console\Commands\MonitorPrices;
use App\Jobs\CheckAdPriceJob;
use App\Models\Ad;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class MonitorPricesTest extends TestCase
{
    use RefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_dispatches_check_ad_price_jobs_for_all_ads()
    {
        $ads = Ad::factory()->count(3)->create();

        Queue::fake();

        Artisan::call('monitor:prices');

        foreach ($ads as $ad) {
            Queue::assertPushed(CheckAdPriceJob::class, function ($job) use ($ad) {
                return $job->ad->id === $ad->id;
            });
        }

        Queue::assertPushed(CheckAdPriceJob::class, $ads->count());
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_handles_no_ads_gracefully()
    {
        Queue::fake();

        Artisan::call('monitor:prices');

        Queue::assertNothingPushed();
    }
}

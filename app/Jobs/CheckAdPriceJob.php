<?php
namespace App\Jobs;

use App\Models\Ad;
use App\Services\NotificationService;
use App\Services\PriceFetcherService;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CheckAdPriceJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $ad;

    public function __construct(Ad $ad)
    {
        $this->ad = $ad;
    }

    public function handle(PriceFetcherService $priceFetcherService, NotificationService $notifySubscribers): void
    {
        try {
            $currentPrice = $priceFetcherService->fetchPrice($this->ad->url);
            if ($currentPrice === null) {
                Log::warning("Failed to fetch price for Ad ID: {$this->ad->id}");
            }

            if ($this->ad->current_price !== null && $this->ad->current_price != $currentPrice) {
                $oldPrice = $this->ad->current_price;
                $this->ad->current_price = $currentPrice;
                $this->ad->last_checked_at = now();
                $this->ad->save();

                $notifySubscribers->notifySubscribers($this->ad, $oldPrice, $currentPrice);
            } else {
                $this->ad->current_price = $currentPrice;
                $this->ad->last_checked_at = now();

                $this->ad->save();
            }
        } catch (\Exception $e) {
            Log::error("Error monitoring Ad ID {$this->ad->id}: " . $e->getMessage());
        }
    }
}

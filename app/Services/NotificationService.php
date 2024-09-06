<?php
namespace App\Services;

use App\Models\Ad;
use App\Mail\PriceChangeNotification;
use Illuminate\Support\Facades\Mail;

class NotificationService
{
    public function notifySubscribers(Ad $ad, $oldPrice, $newPrice): void
    {
        $activeSubscriptions = $ad->subscriptions()->where('status', 'active')->get();

        foreach ($activeSubscriptions as $subscription) {
            Mail::to($subscription->email)->send(new PriceChangeNotification($ad, $oldPrice, $newPrice));
        }
    }
}

<?php

namespace Tests\Feature;

use App\Mail\PriceChangeNotification;
use App\Models\Ad;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PriceChangeNotificationTest extends TestCase
{
    use RefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_sends_price_change_notification_email()
    {
        Mail::fake();

        $ad = Ad::factory()->create([
            'url' => 'https://www.olx.com/item/some-ad',
        ]);

        $oldPrice = 1000;
        $newPrice = 900;
        $email = 'user@example.com';

        Mail::to($email)->send(new PriceChangeNotification($ad, $oldPrice, $newPrice));

        Mail::assertSent(PriceChangeNotification::class, function ($mail) use ($email, $ad, $oldPrice, $newPrice) {
            return $mail->hasTo($email)
                && $mail->ad->is($ad)
                && $mail->oldPrice === $oldPrice
                && $mail->newPrice === $newPrice;
        });
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function price_change_notification_contains_correct_data()
    {
        $ad = Ad::factory()->create([
            'url' => 'https://www.olx.com/item/some-ad',
        ]);

        $oldPrice = 1000;
        $newPrice = 900;

        $mail = new PriceChangeNotification($ad, $oldPrice, $newPrice);

        $rendered = strip_tags($mail->render());

        $this->assertStringContainsString('Ad URL: ' . $ad->url, $rendered);
        $this->assertStringContainsString('Old Price: ' . $oldPrice, $rendered);
        $this->assertStringContainsString('New Price: ' . $newPrice, $rendered);
    }
}

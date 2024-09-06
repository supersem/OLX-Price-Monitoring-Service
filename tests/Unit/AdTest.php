<?php

namespace Tests\Unit;

use App\Models\Ad;
use App\Models\Subscription;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class AdTest extends TestCase
{
    use RefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_can_create_an_ad()
    {
        $ad = Ad::factory()->create([
            'url' => 'https://www.olx.com/item/some-ad',
            'current_price' => 1000,
        ]);

        $this->assertDatabaseHas('ads', [
            'id' => $ad->id,
            'url' => 'https://www.olx.com/item/some-ad',
            'current_price' => 1000,
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function an_ad_has_many_subscriptions()
    {
        $ad = Ad::factory()->create();

        $subscriptions = collect();

        for ($i = 0; $i < 3; $i++) {
            $subscriptions->push(Subscription::factory()->create([
                'ad_id' => $ad->id,
                'email' => 'user@example.com',
                'status' => 'pending',
                'verification_token' => Str::random(32)
            ]));
        }

        $this->assertCount(3, $ad->subscriptions);
        $this->assertTrue($ad->subscriptions->contains($subscriptions->first()));
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_can_update_an_ad_price()
    {
        $ad = Ad::factory()->create([
            'current_price' => 1000,
        ]);

        $ad->update(['current_price' => 1500]);

        $this->assertDatabaseHas('ads', [
            'id' => $ad->id,
            'current_price' => 1500,
        ]);
    }
}

<?php

namespace Tests\Unit;

use App\Models\Ad;
use App\Models\Subscription;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubscriptionTest extends TestCase
{
    use RefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_can_create_a_subscription()
    {
        $ad = Ad::factory()->create();

        $subscription = Subscription::factory()->create([
            'ad_id' => $ad->id,
            'email' => 'user@example.com',
            'verification_token' => '123456',
        ]);

        $this->assertDatabaseHas('subscriptions', [
            'id' => $subscription->id,
            'email' => 'user@example.com',
            'verification_token' => '123456',
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function a_subscription_belongs_to_an_ad()
    {
        $ad = Ad::factory()->create();

        $subscription = Subscription::factory()->create([
            'ad_id' => $ad->id,
            'email' => 'user@example.com',
            'verification_token' => '123456'
        ]);

        $this->assertInstanceOf(Ad::class, $subscription->ad);
        $this->assertTrue($subscription->ad->is($ad));
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_can_verify_a_subscription()
    {
        $ad = Ad::factory()->create();

        $subscription = Subscription::factory()->create([
            'ad_id' => $ad->id,
            'email' => 'user@example.com',
            'status' => 'pending',
            'verification_token' => '123456'
        ]);

        $subscription->update(['status' => 'active']);

        $this->assertDatabaseHas('subscriptions', [
            'id' => $subscription->id,
            'status' => 'active',
        ]);
    }
}

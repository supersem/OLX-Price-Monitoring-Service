<?php

namespace Tests\Feature;

use Illuminate\Support\Str;
use Tests\TestCase;
use App\Models\Ad;
use App\Models\Subscription;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use App\Mail\VerificationEmail;

class SubscriptionControllerTest extends TestCase
{
    use RefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_allows_user_to_subscribe_to_ad_price_changes()
    {
        Mail::fake();

        $ad = Ad::factory()->create([
            'url' => 'https://olx.com/ad-url',
            'current_price' => 1000,
        ]);

        $response = $this->postJson('/api/subscribe', [
            'ad_url' => $ad->url,
            'email' => 'user@example.com',
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('subscriptions', [
            'ad_id' => $ad->id,
            'email' => 'user@example.com',
        ]);

        Mail::assertSent(VerificationEmail::class, function ($mail) {
            return $mail->hasTo('user@example.com');
        });
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_fails_when_email_is_invalid()
    {
        $response = $this->postJson('/api/subscribe', [
            'ad_url' => 'https://olx.com/ad-url',
            'email' => 'invalid-email',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['email']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_fails_when_ad_url_is_invalid()
    {
        $response = $this->postJson('/api/subscribe', [
            'ad_url' => 'not-a-valid-url',
            'email' => 'user@example.com',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['ad_url']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_prevents_duplicate_subscriptions_for_same_ad()
    {
        Mail::fake();

        $ad = Ad::create(['url' => 'https://www.olx.com/ad/12345']);

        Subscription::factory()->create([
            'ad_id' => $ad->id,
            'email' => 'user@example.com',
            'verification_token' => Str::random(32),
        ]);

        $response = $this->postJson('/api/subscribe', [
            'ad_url' => $ad->url,
            'email' => 'user@example.com',
        ]);

        $response->assertStatus(409);  // Conflict or similar code indicating a duplicate
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function testConfirmSubscription()
    {
        $ad = Ad::create(['url' => 'https://www.olx.com/ad/12345']);
        $subscription = Subscription::create([
            'ad_id' => $ad->id,
            'email' => 'user@example.com',
            'verification_token' => Str::random(32),
            'status' => 'pending',
        ]);

        $response = $this->get('/api/confirm/' . $subscription->verification_token);

        $response->assertStatus(200)
            ->assertJson(['message' => 'Subscription confirmed successfully.']);

        $this->assertDatabaseHas('subscriptions', [
            'id' => $subscription->id,
            'status' => 'active',
        ]);
    }
}

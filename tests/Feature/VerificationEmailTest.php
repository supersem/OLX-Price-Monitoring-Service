<?php

namespace Tests\Feature;

use App\Mail\VerificationEmail;
use App\Models\Ad;
use App\Models\Subscription;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class VerificationEmailTest extends TestCase
{
    use RefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_sends_verification_email()
    {
        Mail::fake();

        $ad = Ad::factory()->create([
            'url' => 'https://www.olx.com/item/some-ad',
        ]);

        $subscription = Subscription::factory()->create([
            'ad_id' => $ad->id,
            'email' => 'user@example.com',
            'verification_token' => '123456'
        ]);

        Mail::to($subscription->email)->send(new VerificationEmail($subscription));

        Mail::assertSent(VerificationEmail::class, function ($mail) use ($subscription) {
            return $mail->hasTo($subscription->email)
                && $mail->subscription->is($subscription);
        });
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function verification_email_contains_correct_data()
    {
        Mail::fake();

        $ad = Ad::factory()->create([
            'url' => 'https://www.olx.com/item/some-ad',
        ]);

        $subscription = Subscription::factory()->create([
            'ad_id' => $ad->id,
            'email' => 'user2@example.com',
            'verification_token' => '123456'
        ]);

        $mail = new VerificationEmail($subscription);

        $rendered = $mail->render();

        $this->assertStringContainsString('Please confirm your subscription by clicking the link below:' , $rendered);
        $this->assertStringContainsString($subscription->verification_token, $rendered);
    }

}

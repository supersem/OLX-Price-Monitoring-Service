<?php

namespace Tests\Unit;

use Illuminate\Database\Eloquent\Collection;
use Tests\TestCase;
use App\Services\NotificationService;
use App\Models\Ad;
use App\Models\Subscription;
use App\Mail\PriceChangeNotification;
use Illuminate\Support\Facades\Mail;
use Mockery;

class NotificationServiceTest extends TestCase
{
    /** @var NotificationService */
    protected $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new NotificationService();
        Mail::fake();
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function testNotifySubscribersSendsEmailsToActiveSubscribers()
    {
        $ad = Mockery::mock(Ad::class);

        $queryMock = Mockery::mock('Illuminate\Database\Eloquent\Builder');
        $queryMock->shouldReceive('where')->with('status', 'active')->andReturn($queryMock);
        $queryMock->shouldReceive('get')->andReturn(new Collection([
            new Subscription(['email' => 'active1@example.com', 'status' => 'active']),
            new Subscription(['email' => 'active2@example.com', 'status' => 'active']),
        ]));

        $ad->shouldReceive('subscriptions')->andReturn($queryMock);

        $this->service->notifySubscribers($ad, 100.00, 120.00);

        Mail::assertSent(PriceChangeNotification::class, function ($mail) {
            return $mail->hasTo('active1@example.com') || $mail->hasTo('active2@example.com');
        });
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function testNotifySubscribersHandlesNoSubscriptions()
    {
        $ad = Mockery::mock(Ad::class);

        $queryMock = Mockery::mock('Illuminate\Database\Eloquent\Builder');
        $queryMock->shouldReceive('where')->with('status', 'active')->andReturn($queryMock);
        $queryMock->shouldReceive('get')->andReturn(new Collection()); // Return empty collection

        $ad->shouldReceive('subscriptions')->andReturn($queryMock);

        $this->service->notifySubscribers($ad, 100.00, 120.00);

        Mail::assertNotSent(PriceChangeNotification::class);
    }
}


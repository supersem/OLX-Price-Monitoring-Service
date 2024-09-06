<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\PriceFetcherService;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use Symfony\Component\DomCrawler\Crawler;

class PriceFetcherServiceTest extends TestCase
{
    public $service;
    public $client;

    protected function setUp(): void
    {
        parent::setUp();

        $this->client = $this->createMock(Client::class);
        $this->service = new PriceFetcherService();
        $this->service->client = $this->client;
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function testFetchPriceSuccess()
    {
        $url = 'https://example.com/ad';

        $html = '<html><body><span class="css-90xrc0">123,45</span></body></html>';
        $this->client->method('get')
            ->willReturn(new Response(200, [], $html));

        $price = $this->service->fetchPrice($url);

        $this->assertIsFloat($price);
        $this->assertEquals(123.45, $price);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function testFetchPriceHandlesError()
    {
        $url = 'https://example.com/ad';

        $this->client->method('get')
            ->will($this->throwException(new \Exception('Error fetching page')));

        $price = $this->service->fetchPrice($url);

        $this->assertNull($price);
    }

    public function testParsePrice()
    {
        $this->assertEquals(123.45, $this->service->parsePrice('123,45 €'));
        $this->assertEquals(300.75, $this->service->parsePrice('300.75 USD'));
    }
}

<?php
namespace App\Services;

use GuzzleHttp\Client;
use Symfony\Component\DomCrawler\Crawler;
use Illuminate\Support\Facades\Log;

class PriceFetcherService
{
    public $client;

    public function __construct()
    {
        $this->client = new Client([
            'timeout' => 10.0,
        ]);
    }

    /**
     * Fetch the current price of an OLX ad by parsing its web page.
     */
    public function fetchPrice($url): ?float
    {
        try {
            $response = $this->client->get($url);
            $html = $response->getBody()->getContents();

            $crawler = new Crawler($html);

            $priceElement = $crawler->filter('.css-90xrc0');

            if ($priceElement->count() > 0) {
                $priceText = $priceElement->text();
                return $this->parsePrice($priceText);
            }

            return null;
        } catch (\Exception $e) {
            // Log the error and return null
            Log::error("Error fetching price from {$url}: " . $e->getMessage());
            return null;
        }
    }

    public function parsePrice($priceText): float
    {
        $price = preg_replace('/[^\d.,]/', '', $priceText);

        $price = str_replace(',', '.', $price);

        return floatval($price);
    }
}

<?php
require __DIR__ . '/../vendor/autoload.php';
require_once 'ExchangeRateProvider.php';

use GuzzleHttp\Client;


class ApiLayerExchangeRateProvider implements ExchangeRateProvider
{
    private $apiUrl;
    private $apiKey;
    private $client;

    public function __construct($apiKey, $apiUrl)
    {
        $this->apiKey = $apiKey;
        $this->apiUrl = $apiUrl;
        $this->client = new Client([
            'verify' => false,
        ]);
    }

    public function getExchangeRate($currency)
    {
        if ($currency === 'EUR') {
            return 1.0;
        }
        
        try {
            $url = $this->apiUrl . "/exchangerates_data/latest?symbols=" . $currency . "&base=EUR";
            $response = $this->client->request('GET', $url, [
                'headers' => [
                    'apikey' => $this->apiKey,
                    'Content-Type' => 'text/plain',

                ]
            ]);
            $exchangeRates = json_decode($response->getBody()->getContents(), true);
            return $exchangeRates['rates'][$currency];
        } catch (RequestException $e) {
            throw new Exception('Error fetching exchange rate: ' . $e->getMessage());
        }
    }
}

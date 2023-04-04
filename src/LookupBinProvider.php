<?php
require __DIR__ . '/../vendor/autoload.php';
require_once 'BinProvider.php';

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class LookupBinProvider implements BinProvider
{
    private $apiUrl;
    private $apiKey;
    private $client;

    public function __construct($apiUrl, $apiKey)
    {
        $this->apiKey = $apiKey;
        $this->apiUrl = $apiUrl;
        $this->client = new Client([
            'verify' => false,
        ]);
    }

    public function getBinDetails($code)
    {
        try {
            $url = $this->apiUrl.$code;
            $response = $this->client->request('GET',$url);
            $binResults = json_decode($response->getBody()->getContents());
            return $binResults->country->alpha2;
        } catch (RequestException $e) {
            throw new Exception('Error fetching bin details: ' . $e->getMessage());
        }
    }
}

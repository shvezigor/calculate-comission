<?php
require_once 'src\Calculator.php';
require_once 'src\ApiLayerExchangeRateProvider.php';
require_once 'src\LookupBinProvider.php';

$binApiUrl      = 'https://lookup.binlist.net/';
$apiKeyBin      = '';

$exchangeApiUrl = 'https://api.apilayer.com';
$apiKeyExchange = 'XjBQm3D7wkzZuhtzOgjYQEv6GdrHYIdG';


$exchangeRateProvider = new Calculator(new LookupBinProvider($binApiUrl, $apiKeyBin), new ApiLayerExchangeRateProvider($apiKeyExchange, $exchangeApiUrl));

$data = file_get_contents('input.txt');
$res = $exchangeRateProvider->calculateFee($data);
echo $res;





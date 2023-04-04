<?php
require __DIR__ . '/../vendor/autoload.php';

require_once 'ExchangeRateProvider.php';
require_once 'BinProvider.php';

class Calculator
{
    private $binProvider;
    private $exchangeRateProvider;
    
    public function __construct(BinProvider $binProvider, ExchangeRateProvider $exchangeRateProvider)
    {
        $this->binProvider = $binProvider;
        $this->exchangeRateProvider = $exchangeRateProvider;
        
    }

    public function calculateFee($input)
    {
        $rows = explode("\n", $input);
        $result = '';
        foreach ($rows as $row) {
            if (empty($row)) {
                continue;
            }

            $data = json_decode($row, true);
            
            $countryCode =$this->binProvider->getBinDetails($data['bin']);
            
            if (!$countryCode) {
                throw new Exception('Unable to fetch bin details');
            }

            $isEu = $this->isEu($countryCode);

            $rate = $this->exchangeRateProvider->getExchangeRate($data['currency']);
            if ($data['currency'] == 'EUR' || $rate == 0) {
                $amount = $data['amount'];
            } else {
                $amount =$data['amount']/ $rate;
            }

            $fee = $amount * ($isEu ? 0.01 : 0.02);
            $result .= number_format($fee, 2) . "\n";
        }

        return $result;
    }

    private function isEu($countryCode)
    {
        $euCountries = [
            'AT', 'BE', 'BG', 'CY', 'CZ', 'DE', 'DK', 'EE', 'ES', 'FI',
            'FR', 'GR', 'HR', 'HU', 'IE', 'IT', 'LT', 'LU', 'LV', 'MT',
            'NL', 'PO', 'PT', 'RO', 'SE', 'SI', 'SK'
        ];

        return in_array($countryCode, $euCountries);
    }

}
<?php

use PHPUnit\Framework\TestCase;

require_once '.\src\Calculator.php';
require_once '.\src\ApiLayerExchangeRateProvider.php';
require_once '.\src\LookupBinProvider.php';

class CalculatorTest extends TestCase
{

    public function testCalculateFee()
    {
        // Mock the BinProvider and ExchangeRateProvider
        $binProviderMock = $this->getMockBuilder(BinProvider::class)
            ->getMock();
        $binProviderMock->method('getBinDetails')
            ->willReturn('FR'); // Return a fixed country code for testing purposes

        $exchangeRateProviderMock = $this->getMockBuilder(ExchangeRateProvider::class)
            ->getMock();
        $exchangeRateProviderMock->method('getExchangeRate')
            ->willReturn(1.2); // Return a fixed exchange rate for testing purposes

        // Instantiate the Calculator with the mocked providers
        $calculator = new Calculator($binProviderMock, $exchangeRateProviderMock);

        // Test with a single input row
        $input = '{"bin": "123456", "currency": "USD", "amount": 100}';
        $expectedOutput = "0.40\n"; // Expected fee for a non-EU country with a USD transaction of 100

        $this->assertEquals($expectedOutput, $calculator->calculateFee($input));

        // Test with multiple input rows
        $input = '{"bin": "123456", "currency": "USD", "amount": 100}' . "\n" .
                 '{"bin": "789012", "currency": "EUR", "amount": 50}';
        $expectedOutput = "2.40\n0.50\n"; // Expected fees for a non-EU USD transaction of 100 and an EU EUR transaction of 50

        $this->assertEquals($expectedOutput, $calculator->calculateFee($input));
    }

    public function testCalculateFeeWithInvalidBin()
    {
        // Mock the BinProvider to return false, indicating an invalid bin input
        $binProviderMock = $this->createMock(BinProvider::class);
        $binProviderMock->expects($this->once())
            ->method('getBinDetails')
            ->with('123456')
            ->willReturn(false);

        $exchangeRateProviderMock = $this->createMock(ExchangeRateProvider::class);

        // Instantiate the Calculator with the mocked BinProvider and ExchangeRateProvider
        $calculator = new Calculator($binProviderMock, $exchangeRateProviderMock);

        // Test with an invalid bin input
        $input = '{"bin":"123456","amount":"100.00","currency":"EUR"}';

        $this->expectException(Exception::class);

        $calculator->calculateFee($input);
    }
}

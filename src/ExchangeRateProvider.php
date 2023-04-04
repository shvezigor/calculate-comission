<?php
interface ExchangeRateProvider
{
    public function getExchangeRate($currency);
}
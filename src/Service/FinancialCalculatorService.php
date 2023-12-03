<?php

declare(strict_types=1);

namespace App\Service;

use App\Service\Calculator\GrossPriceCalculator;
use App\Service\Calculator\NetPriceCalculator;
use App\Service\Calculator\VatAmountCalculator;

class FinancialCalculatorService
{
    private $netPriceCalculator;
    private $vatAmountCalculator;
    private $grossPriceCalculator;

    public function __construct()
    {
        $this->netPriceCalculator = new NetPriceCalculator;
        $this->vatAmountCalculator = new VatAmountCalculator;
        $this->grossPriceCalculator = new GrossPriceCalculator;
    }

    public function calculatePrices(int $price, int $vatRate): array
    {
        $netPrice = $this->netPriceCalculator->calculate($price, $vatRate);
        $vatAmount = $this->vatAmountCalculator->calculate($netPrice, $vatRate);
        $grossPrice = $this->grossPriceCalculator->calculate($netPrice, $vatRate);

        return [
            'netPrice' => $netPrice,
            'vatAmount' => $vatAmount,
            'grossPrice' => $grossPrice
        ];
    }
}
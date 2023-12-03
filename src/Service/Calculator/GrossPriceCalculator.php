<?php

declare(strict_types=1);

namespace App\Service\Calculator;

class GrossPriceCalculator implements Calculator
{
    public function calculate(int $price, int $vatRate): int
    {
        return (int) round($price * (1 + $vatRate / 100.0));
    }
}
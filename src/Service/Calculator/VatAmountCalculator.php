<?php

declare(strict_types=1);

namespace App\Service\Calculator;

class VatAmountCalculator implements Calculator
{
    public function calculate(int $price, int $vatRate): int
    {
        return (int) round($price * ($vatRate / 100.0));
    }
}
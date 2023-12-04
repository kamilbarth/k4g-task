<?php

declare(strict_types=1);

namespace App\Service\Calculator;

interface Calculator
{
    public function calculate(int $price, int $vatRate): int;
}
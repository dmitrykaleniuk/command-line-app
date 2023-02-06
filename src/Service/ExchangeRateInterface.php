<?php

declare(strict_types=1);

namespace App\Service;

interface ExchangeRateInterface
{
    public function get(string $currency): float;
}

<?php

declare(strict_types=1);

namespace App\Service;

interface BinToCountryCodeConverterInterface
{
    public function convert(string $binNumber): string;
}

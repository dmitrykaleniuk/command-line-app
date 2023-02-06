<?php

declare(strict_types=1);

namespace App\Common\Http\DTO;

final class PaymentDTO
{
    public const BIN      = 'bin';
    public const AMOUNT   = 'amount';
    public const CURRENCY = 'currency';

    public const REQUIRED_FIELDS = [
        self::BIN,
        self::AMOUNT,
        self::CURRENCY
    ];

    public function __construct(
        private readonly string $binNumber,
        private readonly string $amount,
        private readonly string $currency
    ) {
    }

    public function getBinNumber(): string
    {
        return $this->binNumber;
    }

    public function getAmount(): string
    {
        return $this->amount;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }
}

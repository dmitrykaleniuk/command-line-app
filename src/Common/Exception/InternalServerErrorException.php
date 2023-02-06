<?php

declare(strict_types=1);

namespace App\Common\Exception;

class InternalServerErrorException extends \RuntimeException
{
    public static function create(\Throwable $previous = null): self
    {
        return new self('Internal server error! Try again later or contact support.', 500, $previous);
    }

    public static function createFromMessage(string $message): self
    {
        return new self($message);
    }
}

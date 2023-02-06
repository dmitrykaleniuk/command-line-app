<?php

declare(strict_types=1);

namespace App\Common\Exception;

class HttpClientException extends \RuntimeException
{
    public static function create(\Throwable $previous = null): self
    {
        return new self('Internal server error! Try again later or contact support.',500, $previous);
    }
}

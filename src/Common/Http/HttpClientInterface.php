<?php

declare(strict_types=1);

namespace App\Common\Http;

use Psr\Http\Message\ResponseInterface;

interface HttpClientInterface
{
    public function get(string $uri, array $options = []): ResponseInterface;
}

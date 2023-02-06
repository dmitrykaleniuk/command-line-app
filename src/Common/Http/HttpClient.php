<?php

declare(strict_types=1);

namespace App\Common\Http;

use App\Common\Exception\HttpClientException;
use App\Common\Exception\InternalServerErrorException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;

class HttpClient implements HttpClientInterface
{
    private const GET_METHOD = 'GET';

    private Client $client;

    public function __construct()
    {
        $this->client = new Client();
    }

    public function get(string $uri, array $options = []): ResponseInterface
    {
        return $this->request(self::GET_METHOD, $uri, $options);
    }

    private function request(string $method, string $uri, array $options = []): ResponseInterface
    {
        try {
            return $this->client->request($method, $uri, $options);
        } catch (ClientException $e) {
            throw HttpClientException::create($e);
        } catch (GuzzleException $e) {
            throw InternalServerErrorException::createFromMessage($e->getMessage());
        }
    }
}

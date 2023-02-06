<?php

declare(strict_types=1);

namespace App\Service;

use App\Common\Exception\InternalServerErrorException;
use App\Common\Http\HttpClientInterface;

class ApiLayerExchangeRate implements ExchangeRateInterface
{
    private const RATES                 = 'rates';
    private const API_KEY               = 'apikey';
    private const EXCHANGE_SITE_VAR_KEY = 'EXCHANGE_SITE_URL';
    private const API_LAYER_VAR_KEY     = 'API_LAYER_KEY';

    public function __construct(private readonly HttpClientInterface $httpClient)
    {
    }

    public function get(string $currency): float
    {
        $apiKey = $_ENV[self::API_LAYER_VAR_KEY];
        if (empty($apiKey)) {
            throw InternalServerErrorException::createFromMessage('Missing ENV variable API_LAYER_KEY!');
        }

        $exchangeUrl = $_ENV[self::EXCHANGE_SITE_VAR_KEY];
        if (empty($exchangeUrl)) {
            throw InternalServerErrorException::createFromMessage(
                \sprintf('Missing ENV variable %s!', self::EXCHANGE_SITE_VAR_KEY)
            );
        }

        $response = $this->httpClient->get($exchangeUrl, [
            'headers' => [
                self::API_KEY => $apiKey
            ]
        ]);

        $responseData = \json_decode($response->getBody()->getContents(), true);

        if (\array_key_exists(self::RATES, $responseData) && \array_key_exists($currency, $responseData[self::RATES])) {
            return $responseData[self::RATES][$currency];
        }

        throw InternalServerErrorException::createFromMessage('Impossible to receive exchange rate! Try again later!');
    }
}

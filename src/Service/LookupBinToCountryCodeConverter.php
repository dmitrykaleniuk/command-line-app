<?php

declare(strict_types=1);

namespace App\Service;

use App\Common\Exception\InternalServerErrorException;
use App\Common\Http\HttpClientInterface;

class LookupBinToCountryCodeConverter implements BinToCountryCodeConverterInterface
{
    private const COUNTRY_FIELD = 'country';
    private const ALPHA2_FIELD  = 'alpha2';

    public function __construct(private readonly HttpClientInterface $httpClient)
    {
    }

    public function convert(string $binNumber): string
    {
        $binUrl = $_ENV['BIN_URL'];
        if (empty($binUrl)) {
            throw InternalServerErrorException::createFromMessage('Missing ENV variable BIN_URL!');
        }

        $response = $this->httpClient->get($binUrl . $binNumber);

        $responseData = \json_decode($response->getBody()->getContents(), true);

        if (\array_key_exists(self::COUNTRY_FIELD, $responseData)
            && \array_key_exists(self::ALPHA2_FIELD, $responseData[self::COUNTRY_FIELD])) {
            return $responseData[self::COUNTRY_FIELD][self::ALPHA2_FIELD];
        }

        throw InternalServerErrorException::createFromMessage('Impossible to convert BIN to countryCode!');
    }
}

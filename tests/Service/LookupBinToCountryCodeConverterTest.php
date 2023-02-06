<?php

namespace Tests\Service;

use App\Common\Exception\InternalServerErrorException;
use App\Common\Http\HttpClientInterface;
use App\Service\BinToCountryCodeConverterInterface;
use App\Service\LookupBinToCountryCodeConverter;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Symfony\Component\Dotenv\Dotenv;

class LookupBinToCountryCodeConverterTest extends TestCase
{
    private const BIN_NUMBER     = 516793;
    private const STREAM_CONTENT = '{"country": {"alpha2": "LT"}}';
    private const INVALID_STREAM = '{"country": {"alpha1": "UA"}}';
    private const COUNTRY_CODE   = 'LT';

    private BinToCountryCodeConverterInterface $binToCountryCodeConverter;
    private HttpClientInterface $httpClient;
    private ResponseInterface $response;
    private StreamInterface $stream;

    public function setUp(): void
    {
        if (\method_exists(Dotenv::class, 'bootEnv')) {
            (new Dotenv())->bootEnv(\dirname(__DIR__) . '/../.env');
        }

        $this->httpClient = $this->getMockBuilder(HttpClientInterface::class)->getMock();
        $this->response = $this->getMockBuilder(ResponseInterface::class)->getMock();
        $this->stream = $this->getMockBuilder(StreamInterface::class)->getMock();
        $this->binToCountryCodeConverter = new LookupBinToCountryCodeConverter($this->httpClient);
    }

    public function testConvert(): void
    {
        $this->configureHttpClientMock();
        $this->configureResponse();
        $this->configureStream();

        $response = $this->binToCountryCodeConverter->convert(self::BIN_NUMBER);

        self::assertEquals(self::COUNTRY_CODE, trim($response));
    }

    public function testConvertWithInvalidRateResponse(): void
    {
        $this->configureHttpClientMock();
        $this->configureResponse();
        $this->configureInvalidStream();

        self::expectException(InternalServerErrorException::class);
        self::expectExceptionMessage('Impossible to convert BIN to countryCode!');
        $this->binToCountryCodeConverter->convert(self::BIN_NUMBER);
    }

    private function configureStream(): void
    {
        $this->stream
            ->expects($this->once())
            ->method('getContents')
            ->willReturn(self::STREAM_CONTENT);
    }

    private function configureInvalidStream(): void
    {
        $this->stream
            ->expects($this->once())
            ->method('getContents')
            ->willReturn(self::INVALID_STREAM);
    }

    private function configureResponse(): void
    {
        $this->response
            ->expects($this->once())
            ->method('getBody')
            ->willReturn($this->stream);
    }

    private function configureHttpClientMock(): void
    {
        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->willReturn($this->response);
    }
}

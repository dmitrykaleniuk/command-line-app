<?php

namespace Tests\Service;

use App\Common\Exception\InternalServerErrorException;
use App\Common\Http\HttpClientInterface;
use App\Service\ApiLayerExchangeRate;
use App\Service\ExchangeRateInterface;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Symfony\Component\Dotenv\Dotenv;

class ApiLayerExchangeRateTest extends TestCase
{
    private const EXCHANGE_RATE  = 1.076197;
    private const STREAM_CONTENT = '{"base": "EUR","rates": {"USD": 1.076197}}';
    private const INVALID_STREAM = '{"base": "EUR","rates": {"UAH": 111}}';
    private const CURRENCY       = 'USD';

    private ExchangeRateInterface $exchangeRate;
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
        $this->exchangeRate = new ApiLayerExchangeRate($this->httpClient);
    }

    public function testGet(): void
    {
        $this->configureHttpClientMock();
        $this->configureResponse();
        $this->configureStream();

        $response = $this->exchangeRate->get(self::CURRENCY);

        self::assertEquals(self::EXCHANGE_RATE, trim($response));
    }

    public function testGetWithInvalidRateResponse(): void
    {
        $this->configureHttpClientMock();
        $this->configureResponse();
        $this->configureInvalidStream();

        self::expectException(InternalServerErrorException::class);
        self::expectExceptionMessage('Impossible to receive exchange rate! Try again later!');
        $this->exchangeRate->get(self::CURRENCY);
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

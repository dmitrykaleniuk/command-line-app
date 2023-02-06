<?php

namespace Tests\Command;

use App\Command\CalculateTax;
use App\Service\BinToCountryCodeConverterInterface;
use App\Service\ExchangeRateInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Tester\CommandTester;

class CalculateTaxTest extends TestCase
{
    private const FIXTURES_DIR         = __DIR__ . '/../fixtures/txt/%s';
    private const VALID_FILE           = 'valid_input.txt';
    private const INVALID_FILE         = 'invalid_input.txt';
    private const EXPECTED_TAX         = '0.46';
    private const EXCHANGE_RATE        = 1.076137;
    private const COUNTRY_CODE         = 'LT';
    private const MISSING_FIELDS_ERROR = '! [CAUTION] Missing fields in the payment data: amount, currency';

    private ExchangeRateInterface $mockExchangeRate;
    private BinToCountryCodeConverterInterface $mockBinToCountryConverter;

    public function setUp(): void
    {
        $this->mockExchangeRate = $this->getMockBuilder(ExchangeRateInterface::class)->getMock();
        $this->mockBinToCountryConverter = $this->getMockBuilder(BinToCountryCodeConverterInterface::class)->getMock();
    }

    public function testCalculateTaxWithValidInput(): void
    {
        $this->configureExchangeRateMock();
        $this->configureBinToCountryCodeConverterMock();

        $command = new CalculateTax($this->mockExchangeRate, $this->mockBinToCountryConverter);

        $tester = new CommandTester($command);
        $tester->execute(['inputFile' => \sprintf(self::FIXTURES_DIR, self::VALID_FILE)]);

        $tester->assertCommandIsSuccessful();
        self::assertEquals(self::EXPECTED_TAX, trim($tester->getDisplay()));
    }

    public function testCalculateTaxWithInValidInput(): void
    {
        $this->configureExchangeRateMockForInvalidInput();
        $this->configureBinToCountryCodeConverterMockForInvalidInput();

        $command = new CalculateTax($this->mockExchangeRate, $this->mockBinToCountryConverter);

        $tester = new CommandTester($command);
        $tester->execute(['inputFile' => \sprintf(self::FIXTURES_DIR, self::INVALID_FILE)]);

        $tester->assertCommandIsSuccessful();

        self::assertEquals(self::MISSING_FIELDS_ERROR, trim($tester->getDisplay()));
    }

    private function configureExchangeRateMock(): void
    {
        $this->mockExchangeRate
            ->expects($this->once())
            ->method('get')
            ->willReturn(self::EXCHANGE_RATE);
    }

    private function configureBinToCountryCodeConverterMock(): void
    {
        $this->mockBinToCountryConverter
            ->expects($this->exactly(1))
            ->method('convert')
            ->willReturn(self::COUNTRY_CODE);
    }

    private function configureExchangeRateMockForInvalidInput(): void
    {
        $this->mockExchangeRate
            ->expects($this->never())
            ->method('get');
    }

    private function configureBinToCountryCodeConverterMockForInvalidInput(): void
    {
        $this->mockBinToCountryConverter
            ->expects($this->never())
            ->method('convert');
    }
}

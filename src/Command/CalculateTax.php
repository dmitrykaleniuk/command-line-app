<?php

declare(strict_types=1);

namespace App\Command;

use App\Common\Enum\EUCountry;
use App\Common\Exception\InvalidArgumentException;
use App\Common\Http\DTO\PaymentDTO;
use App\Service\BinToCountryCodeConverterInterface;
use App\Service\ExchangeRateInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'calculate-tax')]
class CalculateTax extends Command
{
    private const INPUT_FILE_ARGUMENT = 'inputFile';
    private const ROUND_PRECISION     = 2;
    private const BASE_CURRENCY       = 'EUR';
    private const EU_FEE              = 0.01;
    private const NON_EU_FEE          = 0.02;

    public function __construct(
        private readonly ExchangeRateInterface $exchangeRate,
        private readonly BinToCountryCodeConverterInterface $binToCountryCodeConverter,
        string $name = null)
    {
        parent::__construct($name);
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Calculate tax for old transactions from the file!')
            ->addArgument(self::INPUT_FILE_ARGUMENT, InputArgument::REQUIRED, 'File with payment data.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $fileName = $input->getArgument(self::INPUT_FILE_ARGUMENT);

        if (!\is_file($fileName) || !\is_readable($fileName)) {
            $io->error('Please provide correct file!');
            return Command::FAILURE;
        }

        foreach (\file($fileName) as $paymentDataRow) {
            if (empty(\trim($paymentDataRow))) {
                $io->info('Empty row! Moving forward!');
                continue;
            }

            try {
                $io->text($this->calculate($paymentDataRow));
            } catch (\Throwable $throwable) {
                $io->caution($throwable->getMessage());
            }
        }

        return Command::SUCCESS;
    }

    private function calculate(string $paymentData): string
    {
        $paymentData = $this->parsePaymentData($paymentData);

        $convertedAmount = $this->getConvertedAmount($paymentData);

        $tax = $this->getTaxByCountryCode(
            $this->getCountryCodeByBin($paymentData->getBinNumber())
        );

        return \number_format($tax * $convertedAmount, self::ROUND_PRECISION);
    }

    private function parsePaymentData(string $paymentData): PaymentDTO
    {
        $decodedPayment = \json_decode($paymentData, true);

        if (\is_null($decodedPayment)) {
            throw InvalidArgumentException::createFromMessage('Invalid payment data provided!');
        }

        $missingFields = [];
        foreach (PaymentDTO::REQUIRED_FIELDS as $requiredField) {
            if (!\array_key_exists($requiredField, $decodedPayment)) {
                $missingFields[] = $requiredField;
            }
        }

        if (!empty($missingFields)) {
            throw InvalidArgumentException::createFromMessage(
                \sprintf('Missing fields in the payment data: %s', \implode(', ', $missingFields))
            );
        }

        return new PaymentDTO(
            $decodedPayment['bin'],
            $decodedPayment['amount'],
            $decodedPayment['currency']
        );
    }

    private function getCountryCodeByBin(string $binNumber): string
    {
        return $this->binToCountryCodeConverter->convert($binNumber);
    }

    private function getExchangeRate(string $currency): float
    {
        return $this->exchangeRate->get($currency);
    }

    private function getConvertedAmount(PaymentDTO $paymentDTO): float
    {
        $amount = \floatval($paymentDTO->getAmount());
        if ($paymentDTO->getCurrency() !== self::BASE_CURRENCY) {
            $exchangeRate = $this->getExchangeRate($paymentDTO->getCurrency());
            $amount /= $exchangeRate;
        }

        return $amount;
    }

    private function getTaxByCountryCode(string $countryCode): float
    {
        return EUCountry::isEU($countryCode) ? self::EU_FEE : self::NON_EU_FEE;
    }
}

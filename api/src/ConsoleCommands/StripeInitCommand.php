<?php

declare(strict_types=1);

namespace FantasyAcademy\API\ConsoleCommands;

use FantasyAcademy\API\Services\Stripe\StripeClientInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:stripe:init',
    description: 'Initialize Stripe products and prices for Fantasy Academy membership',
)]
final class StripeInitCommand extends Command
{
    private const string APP_METADATA_VALUE = 'fantasy-academy';
    private const string PRODUCT_NAME = 'Fantasy Academy Membership';
    private const string LOOKUP_KEY_MONTHLY = 'fantasy_academy_monthly';
    private const string LOOKUP_KEY_YEARLY = 'fantasy_academy_yearly';

    public function __construct(
        private readonly StripeClientInterface $stripeClient,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption(
                'monthly-amount',
                null,
                InputOption::VALUE_REQUIRED,
                'Monthly price in cents (default: 999 = 9.99)',
                999,
            )
            ->addOption(
                'currency',
                null,
                InputOption::VALUE_REQUIRED,
                'Currency code (default: eur)',
                'eur',
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $monthlyAmountOption = $input->getOption('monthly-amount');
        $monthlyAmount = is_numeric($monthlyAmountOption) ? (int) $monthlyAmountOption : 999;
        $currencyOption = $input->getOption('currency');
        $currency = is_string($currencyOption) ? $currencyOption : 'eur';
        $yearlyAmount = $monthlyAmount * 10;

        $io->title('Stripe Initialization');

        // Step 1: Find or create product
        $io->section('Product');
        $productId = $this->stripeClient->findProductByMetadata(self::APP_METADATA_VALUE);

        if ($productId !== null) {
            $io->writeln(sprintf('Found existing product: <info>%s</info>', $productId));
        } else {
            $productId = $this->stripeClient->createProduct(self::PRODUCT_NAME, self::APP_METADATA_VALUE);
            $io->writeln(sprintf('Created new product: <info>%s</info>', $productId));
        }

        // Step 2: Check existing prices
        $io->section('Prices');
        $existingPrices = $this->stripeClient->getPricesByLookupKeys(
            self::LOOKUP_KEY_MONTHLY,
            self::LOOKUP_KEY_YEARLY,
        );

        $existingLookupKeys = [];
        foreach ($existingPrices as $price) {
            $existingLookupKeys[$price->lookupKey] = $price;
        }

        $prices = [];

        // Create monthly price if not exists
        if (isset($existingLookupKeys[self::LOOKUP_KEY_MONTHLY])) {
            $prices['monthly'] = $existingLookupKeys[self::LOOKUP_KEY_MONTHLY];
            $io->writeln(sprintf(
                'Found existing monthly price: <info>%s</info> (%s %s)',
                $prices['monthly']->priceId,
                number_format($prices['monthly']->unitAmount / 100, 2),
                strtoupper($prices['monthly']->currency),
            ));
        } else {
            $prices['monthly'] = $this->stripeClient->createPrice(
                $productId,
                $monthlyAmount,
                $currency,
                'month',
                self::LOOKUP_KEY_MONTHLY,
            );
            $io->writeln(sprintf(
                'Created monthly price: <info>%s</info> (%s %s)',
                $prices['monthly']->priceId,
                number_format($monthlyAmount / 100, 2),
                strtoupper($currency),
            ));
        }

        // Create yearly price if not exists
        if (isset($existingLookupKeys[self::LOOKUP_KEY_YEARLY])) {
            $prices['yearly'] = $existingLookupKeys[self::LOOKUP_KEY_YEARLY];
            $io->writeln(sprintf(
                'Found existing yearly price: <info>%s</info> (%s %s)',
                $prices['yearly']->priceId,
                number_format($prices['yearly']->unitAmount / 100, 2),
                strtoupper($prices['yearly']->currency),
            ));
        } else {
            $prices['yearly'] = $this->stripeClient->createPrice(
                $productId,
                $yearlyAmount,
                $currency,
                'year',
                self::LOOKUP_KEY_YEARLY,
            );
            $io->writeln(sprintf(
                'Created yearly price: <info>%s</info> (%s %s) - ~17%% discount',
                $prices['yearly']->priceId,
                number_format($yearlyAmount / 100, 2),
                strtoupper($currency),
            ));
        }

        // Summary
        $io->section('Summary');
        $io->table(
            ['Type', 'Price ID', 'Amount', 'Lookup Key'],
            [
                [
                    'Monthly',
                    $prices['monthly']->priceId,
                    number_format($prices['monthly']->unitAmount / 100, 2) . ' ' . strtoupper($prices['monthly']->currency),
                    self::LOOKUP_KEY_MONTHLY,
                ],
                [
                    'Yearly',
                    $prices['yearly']->priceId,
                    number_format($prices['yearly']->unitAmount / 100, 2) . ' ' . strtoupper($prices['yearly']->currency),
                    self::LOOKUP_KEY_YEARLY,
                ],
            ],
        );

        $io->success('Stripe initialization complete!');
        $io->note('Prices are looked up by lookup_key at runtime. No additional configuration needed.');

        return Command::SUCCESS;
    }
}

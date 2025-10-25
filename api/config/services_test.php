<?php

declare(strict_types=1);

use FantasyAcademy\API\Services\ProvideIdentity;
use FantasyAcademy\API\Tests\PredictableIdentityProvider;
use Psr\Clock\ClockInterface;
use Symfony\Component\Clock\MockClock;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function(ContainerConfigurator $configurator): void
{
    $services = $configurator->services();

    $services->defaults()
        ->autoconfigure()
        ->autowire()
        ->public();

    // Freeze clock for consistent test timestamps
    $services->set(ClockInterface::class, MockClock::class)
        ->args(['2025-06-06 12:00:00 UTC']);

    // Use predictable UUID provider in tests for deterministic results
    // Tagged with kernel.reset to automatically reset between tests
    $services->set(PredictableIdentityProvider::class)
        ->tag('kernel.reset', ['method' => 'reset']);
    $services->alias(ProvideIdentity::class, PredictableIdentityProvider::class);

    // Data fixtures
    $services->load('FantasyAcademy\\API\\Tests\\DataFixtures\\', __DIR__ . '/../tests/DataFixtures/{*.php}');
};

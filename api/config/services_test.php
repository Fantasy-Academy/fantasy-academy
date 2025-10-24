<?php

declare(strict_types=1);

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

    // Data fixtures
    $services->load('FantasyAcademy\\API\\Tests\\DataFixtures\\', __DIR__ . '/../tests/DataFixtures/{*.php}');
};

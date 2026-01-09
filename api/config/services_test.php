<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use FantasyAcademy\API\Services\ProvideIdentity;
use FantasyAcademy\API\Tests\PredictableIdentityProvider;
use Psr\Clock\ClockInterface;
use Symfony\Component\Clock\MockClock;

return App::config([
    'services' => [
        '_defaults' => [
            'autoconfigure' => true,
            'autowire' => true,
            'public' => true,
        ],
        ClockInterface::class => [
            'class' => MockClock::class,
            'arguments' => ['2025-06-06 12:00:00 UTC'],
        ],
        PredictableIdentityProvider::class => [
            'tags' => [
                ['kernel.reset' => ['method' => 'reset']],
            ],
        ],
        ProvideIdentity::class => '@' . PredictableIdentityProvider::class,
        'FantasyAcademy\\API\\Tests\\DataFixtures\\' => [
            'resource' => '../tests/DataFixtures/{*.php}',
        ],
    ],
]);

<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return App::config([
    'services' => [
        '_defaults' => [
            'autoconfigure' => true,
            'autowire' => true,
            'public' => true,
        ],
        'FantasyAcademy\\API\\Tests\\DataFixtures\\' => [
            'resource' => '../tests/DataFixtures/{*.php}',
        ],
    ],
]);

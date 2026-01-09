<?php

declare(strict_types=1);

namespace Symfony\Component\Routing\Loader\Configurator;

return Routes::config([
    'api_platform' => [
        'resource' => '.',
        'type' => 'api_platform',
        'prefix' => '/api',
    ],
]);

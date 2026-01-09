<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return App::config([
    'api_platform' => [
        'title' => 'Fantasy Academy API',
        'version' => '1.0.0',
        'mapping' => [
            'paths' => [
                '%kernel.project_dir%/src/Api',
                '%kernel.project_dir%/src/Message',
            ],
        ],
        'enable_entrypoint' => false,
        'use_symfony_listeners' => true,
        'formats' => [
            'json' => ['application/json'],
            'jsonld' => ['application/ld+json'],
        ],
        'swagger' => [
            'api_keys' => [
                'JWT' => [
                    'name' => 'Authorization',
                    'type' => 'header',
                ],
            ],
        ],
        'defaults' => [
            'stateless' => true,
            'cache_headers' => [
                'vary' => ['Content-Type', 'Authorization', 'Origin'],
            ],
            'normalization_context' => [
                'skip_null_values' => false,
            ],
        ],
    ],
]);

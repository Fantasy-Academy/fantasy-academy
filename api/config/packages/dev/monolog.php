<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return App::config([
    'monolog' => [
        'handlers' => [
            'main' => [
                'type' => 'stream',
                'path' => 'php://stderr',
                'level' => 'info',
                'formatter' => 'monolog.formatter.line',
                'channels' => ['!event', '!doctrine', '!security', '!cache'],
            ],
            'console' => [
                'type' => 'console',
                'process_psr_3_messages' => false,
                'channels' => ['!event', '!doctrine', '!console'],
            ],
        ],
    ],
]);

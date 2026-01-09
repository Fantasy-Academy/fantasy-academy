<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Monolog\Formatter\LineFormatter;

return App::config([
    'services' => [
        'monolog.formatter.line' => [
            'class' => LineFormatter::class,
            'arguments' => [
                "[%%datetime%%] %%channel%%.%%level_name%%: %%message%% %%context%% %%extra%%\n",
                'Y-m-d H:i:s',
                true,
                true,
            ],
        ],
    ],
]);

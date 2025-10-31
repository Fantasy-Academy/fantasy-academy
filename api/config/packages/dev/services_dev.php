<?php

declare(strict_types=1);

use Monolog\Formatter\LineFormatter;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $configurator): void {
    $services = $configurator->services();

    // Custom line formatter for development with detailed, human-readable output
    $services->set('monolog.formatter.line', LineFormatter::class)
        ->args([
            "[%%datetime%%] %%channel%%.%%level_name%%: %%message%% %%context%% %%extra%%\n",
            'Y-m-d H:i:s',
            true, // Allow inline line breaks
            true, // Ignore empty context and extra
        ]);
};

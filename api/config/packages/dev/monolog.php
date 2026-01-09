<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->extension('monolog', [
        'handlers' => [
            // Main handler - logs warnings and errors to stderr in a human-readable format
            // Excludes noisy channels: event, doctrine, security, request, cache
            'main' => [
                'type' => 'stream',
                'path' => 'php://stderr',
                'level' => 'info',
                'formatter' => 'monolog.formatter.line',
                'channels' => ['!event', '!doctrine', '!security', '!cache'],
            ],
            // Console handler - for CLI commands output
            'console' => [
                'type' => 'console',
                'process_psr_3_messages' => false,
                'channels' => ['!event', '!doctrine', '!console'],
            ],
            // Sentry handler - only warnings and above
            // Sentry logging is handled by sentry-symfony bundle directly
        ],
    ]);
};

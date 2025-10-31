<?php

declare(strict_types=1);

use Monolog\Level;
use Sentry\State\HubInterface;
use Symfony\Config\MonologConfig;

return static function (MonologConfig $monologConfig): void {
    // Main handler - logs warnings and errors to stderr in a human-readable format
    // Excludes noisy channels: event, doctrine, security, request, cache
    $monologConfig->handler('main')
        ->type('stream')
        ->path('php://stderr')
        ->level('info')
        ->formatter('monolog.formatter.line')
        ->channels()
            ->elements(['!event', '!doctrine', '!security', '!cache']);

    // Console handler - for CLI commands output
    $monologConfig->handler('console')
        ->type('console')
        ->processPsr3Messages(false)
        ->channels()
            ->elements(['!event', '!doctrine', '!console']);

    // Sentry handler - only warnings and above
    $monologConfig->handler('sentry')
        ->type('sentry')
        ->level(Level::Warning->value)
        ->hubId(HubInterface::class);
};

<?php

declare(strict_types=1);

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return static function (RoutingConfigurator $routingConfigurator): void {
    $routingConfigurator->import(__DIR__ . '/../src/Controller', 'attribute');

    $routingConfigurator->add('auth', '/api/auth')
        ->methods(['POST']);

    $routingConfigurator->add('swagger_ui', '/')
        ->controller('api_platform.swagger_ui.action');
};

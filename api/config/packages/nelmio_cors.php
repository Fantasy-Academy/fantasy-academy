<?php declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->extension('nelmio_cors', [
        'paths' => [
            '^/api' => [
                'allow_origin' => ['*'],
                'allow_headers' => ['*'],
                'allow_methods' => ['GET', 'OPTIONS', 'POST', 'PUT', 'PATCH', 'DELETE'],
                'skip_same_as_origin' => true,
                'max_age' => 3600,
            ],
        ],
    ]);
};

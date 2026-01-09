<?php declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->extension('framework', [
        'mailer' => [
            'dsn' => '%env(MAILER_DSN)%',
            'envelope' => [
                'sender' => 'robot@fantasy-academy.com',
            ],
            'headers' => [
                'From' => 'Fantasy Academy <robot@fantasy-academy.com>',
            ],
        ],
    ]);
};

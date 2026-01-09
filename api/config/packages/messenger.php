<?php declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\Mailer\Messenger\SendEmailMessage;
use FantasyAcademy\API\Services\Symfony\Messenger\UserAwareMiddleware;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->extension('framework', [
        'messenger' => [
            'failure_transport' => 'failed',
            'buses' => [
                'command_bus' => [
                    'middleware' => [
                        'doctrine_transaction',
                        UserAwareMiddleware::class,
                    ],
                ],
            ],
            'transports' => [
                'sync' => 'sync://',
                'failed' => 'doctrine://default?queue_name=failed',
                'async' => [
                    'dsn' => '%env(MESSENGER_TRANSPORT_DSN)%',
                    'options' => [
                        'auto_setup' => false,
                        'use_notify' => true,
                        'check_delayed_interval' => 2000,
                    ],
                ],
            ],
            'routing' => [
                'FantasyAcademy\API\Events\*' => 'async',
                SendEmailMessage::class => 'async',
            ],
        ],
    ]);
};

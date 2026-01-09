<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use FantasyAcademy\API\Services\Symfony\Messenger\UserAwareMiddleware;
use Symfony\Component\Mailer\Messenger\SendEmailMessage;

return App::config([
    'framework' => [
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
    ],
]);

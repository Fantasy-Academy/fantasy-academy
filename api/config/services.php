<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use FantasyAcademy\API\Services\Doctrine\FixDoctrineMigrationTableSchema;
use FantasyAcademy\API\Services\ProvideIdentity;
use FantasyAcademy\API\Services\ProvideRandomIdentity;
use FantasyAcademy\API\Services\Stripe\StripeClient;
use FantasyAcademy\API\Services\Stripe\StripeClientInterface;
use Monolog\Processor\PsrLogMessageProcessor;
use Symfony\Component\HttpFoundation\Session\Storage\Handler\PdoSessionHandler;

return App::config([
    'parameters' => [
        '.container.dumper.inline_factories' => true,
    ],
    'services' => [
        '_defaults' => [
            'autoconfigure' => true,
            'autowire' => true,
            'public' => true,
        ],
        PdoSessionHandler::class => [
            'arguments' => [env('DATABASE_URL')],
        ],
        PsrLogMessageProcessor::class => [
            'tags' => ['monolog.processor'],
        ],
        'FantasyAcademy\\API\\Repository\\' => [
            'resource' => '../src/Repository/{*Repository.php}',
        ],
        'FantasyAcademy\\API\\MessageHandler\\' => [
            'resource' => '../src/MessageHandler/**/{*.php}',
        ],
        'FantasyAcademy\\API\\ConsoleCommands\\' => [
            'resource' => '../src/ConsoleCommands/**/{*.php}',
        ],
        'FantasyAcademy\\API\\Validation\\' => [
            'resource' => '../src/Validation/**/{*Validator.php}',
        ],
        'FantasyAcademy\\API\\Services\\' => [
            'resource' => '../src/Services/**/{*.php}',
            'exclude' => ['../src/Services/**/Value/'],
        ],
        'FantasyAcademy\\API\\Query\\' => [
            'resource' => '../src/Query/**/{*.php}',
        ],
        'FantasyAcademy\\API\\FormType\\' => [
            'resource' => '../src/FormType/**/{*.php}',
        ],
        ProvideIdentity::class => '@' . ProvideRandomIdentity::class,
        StripeClientInterface::class => '@' . StripeClient::class,
        'FantasyAcademy\\API\\Controller\\' => [
            'resource' => '../src/Controller/**/{*.php}',
        ],
        'FantasyAcademy\\API\\Api\\' => [
            'resource' => '../src/Api/**/{*Provider.php}',
        ],
        FixDoctrineMigrationTableSchema::class => [
            'autoconfigure' => false,
            'arguments' => [
                '$dependencyFactory' => service('doctrine.migrations.dependency_factory'),
            ],
            'tags' => [
                ['doctrine.event_listener' => ['event' => 'postGenerateSchema']],
            ],
        ],
    ],
]);

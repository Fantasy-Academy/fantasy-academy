<?php

declare(strict_types=1);

use Monolog\Processor\PsrLogMessageProcessor;
use FantasyAcademy\API\Services\Doctrine\FixDoctrineMigrationTableSchema;
use FantasyAcademy\API\Services\ProvideIdentity;
use FantasyAcademy\API\Services\ProvideRandomIdentity;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpFoundation\Session\Storage\Handler\PdoSessionHandler;
use function Symfony\Component\DependencyInjection\Loader\Configurator\env;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function(ContainerConfigurator $configurator): void
{
    $parameters = $configurator->parameters();

    # https://symfony.com/doc/current/performance.html#dump-the-service-container-into-a-single-file
    $parameters->set('.container.dumper.inline_factories', true);

    $parameters->set('doctrine.orm.enable_lazy_ghost_objects', true);

    $services = $configurator->services();

    $services->defaults()
        ->autoconfigure()
        ->autowire()
        ->public();

    $services->set(PdoSessionHandler::class)
        ->args([
            env('DATABASE_URL'),
        ]);

    $services->set(PsrLogMessageProcessor::class)
        ->tag('monolog.processor');

    // Repositories
    $services->load('FantasyAcademy\\API\\Repository\\', __DIR__ . '/../src/Repository/{*Repository.php}');

    // Message handlers
    $services->load('FantasyAcademy\\API\\MessageHandler\\', __DIR__ . '/../src/MessageHandler/**/{*.php}');

    // Console commands
    $services->load('FantasyAcademy\\API\\ConsoleCommands\\', __DIR__ . '/../src/ConsoleCommands/**/{*.php}');

    // Validators
    $services->load('FantasyAcademy\\API\\Validation\\', __DIR__ . '/../src/Validation/**/{*Validator.php}');

    // Services
    $services->load('FantasyAcademy\\API\\Services\\', __DIR__ . '/../src/Services/**/{*.php}');
    $services->load('FantasyAcademy\\API\\Query\\', __DIR__ . '/../src/Query/**/{*.php}');
    $services->load('FantasyAcademy\\API\\FormType\\', __DIR__ . '/../src/FormType/**/{*.php}');

    // Explicitly alias ProvideIdentity interface to production implementation
    $services->alias(ProvideIdentity::class, ProvideRandomIdentity::class);

    // API
    $services->load('FantasyAcademy\\API\\Controller\\', __DIR__ . '/../src/Controller/**/{*.php}');
    $services->load('FantasyAcademy\\API\\Api\\', __DIR__ . '/../src/Api/**/{*Provider.php}');

    /** @see https://github.com/doctrine/migrations/issues/1406 */
    $services->set(FixDoctrineMigrationTableSchema::class)
        ->autoconfigure(false)
        ->arg('$dependencyFactory', service('doctrine.migrations.dependency_factory'))
        ->tag('doctrine.event_listener', ['event' => 'postGenerateSchema']);
};

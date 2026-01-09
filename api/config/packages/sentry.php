<?php declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->extension('sentry', [
        'dsn' => '%env(SENTRY_DSN)%',
        'tracing' => [
            'enabled' => true,
        ],
        'register_error_listener' => false,
        'register_error_handler' => false,
        'messenger' => [
            'enabled' => true,
            'capture_soft_fails' => true,
        ],
        'options' => [
            'environment' => '%kernel.environment%',
            'release' => '%env(default::SENTRY_RELEASE)%',
            'send_default_pii' => true,
            'ignore_exceptions' => [
                AccessDeniedException::class,
                NotFoundHttpException::class,
            ],
            'traces_sample_rate' => '%env(float:SENTRY_TRACES_SAMPLE_RATE)%',
            'profiles_sample_rate' => '%env(float:SENTRY_PROFILES_SAMPLE_RATE)%',
        ],
    ]);
};

<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use FantasyAcademy\API\Entity\User;
use Symfony\Component\Security\Core\Authorization\Voter\AuthenticatedVoter;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

return App::config([
    'security' => [
        'providers' => [
            'user_provider' => [
                'entity' => [
                    'class' => User::class,
                    'property' => 'email',
                ],
            ],
        ],
        'password_hashers' => [
            PasswordAuthenticatedUserInterface::class => [
                'algorithm' => 'auto',
            ],
        ],
        'firewalls' => [
            'dev' => [
                'pattern' => '^/(_profiler|_wdt|css|images|js|assets)/',
                'security' => false,
            ],
            'stateless' => [
                'pattern' => '^(/-/health-check)',
                'stateless' => true,
                'security' => false,
            ],
            'webhooks' => [
                'pattern' => '^/api/webhooks/',
                'stateless' => true,
                'security' => false,
            ],
            'main' => [
                'pattern' => '^/api',
                'stateless' => true,
                'provider' => 'user_provider',
                'jwt' => [],
                'json_login' => [
                    'check_path' => '/api/login',
                    'username_path' => 'email',
                    'password_path' => 'password',
                    'success_handler' => 'lexik_jwt_authentication.handler.authentication_success',
                    'failure_handler' => 'lexik_jwt_authentication.handler.authentication_failure',
                ],
            ],
            'admin' => [
                'pattern' => '^/(?!api)',
                'provider' => 'user_provider',
                'form_login' => [
                    'login_path' => 'login',
                    'check_path' => 'login',
                    'enable_csrf' => true,
                    'default_target_path' => '/admin/import-challenges',
                ],
                'logout' => [
                    'path' => 'logout',
                    'target' => '/',
                    'invalidate_session' => true,
                ],
            ],
        ],
        'access_control' => [
            ['path' => '^/api/me', 'roles' => [AuthenticatedVoter::IS_AUTHENTICATED_FULLY]],
            ['path' => '^/api/subscription', 'roles' => [AuthenticatedVoter::IS_AUTHENTICATED_FULLY]],
            ['path' => '^/api/question/answer', 'roles' => [AuthenticatedVoter::IS_AUTHENTICATED_FULLY]],
            ['path' => '^/$', 'roles' => [AuthenticatedVoter::PUBLIC_ACCESS]],
            ['path' => '^/admin', 'roles' => ['ROLE_ADMIN']],
            ['path' => '^/', 'roles' => [AuthenticatedVoter::PUBLIC_ACCESS]],
        ],
    ],
]);

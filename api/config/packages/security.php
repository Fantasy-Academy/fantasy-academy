<?php

declare(strict_types=1);

use Symfony\Config\Security\PasswordHasherConfig;
use FantasyAcademy\API\Entity\User;
use Symfony\Component\Security\Core\Authorization\Voter\AuthenticatedVoter;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Config\SecurityConfig;

return static function (SecurityConfig $securityConfig): void {
    $securityConfig->provider('user_provider')
        ->entity()
            ->class(User::class)
            ->property('email');

    /** @var PasswordHasherConfig $hasher */
    $hasher = $securityConfig->passwordHasher(PasswordAuthenticatedUserInterface::class);
    $hasher->algorithm('auto');

    $securityConfig->firewall('dev')
        ->pattern('^/(_profiler|_wdt|css|images|js|assets)/')
        ->security(false);

    $securityConfig->firewall('stateless')
        ->pattern('^(/-/health-check)')
        ->stateless(true)
        ->security(false);

    $mainFirewall = $securityConfig->firewall('main');
    $mainFirewall
        ->pattern('^/api')
        ->stateless(true)
        ->provider('user_provider')
        ->stateless(true)
        ->jwt();

    $mainFirewall
        ->jsonLogin()
            ->checkPath('/api/login')
            ->usernamePath('email')
            ->passwordPath('password')
            ->successHandler('lexik_jwt_authentication.handler.authentication_success')
            ->failureHandler('lexik_jwt_authentication.handler.authentication_failure');

    $securityConfig->accessControl()
        ->path('^/api/me')
        ->roles([AuthenticatedVoter::IS_AUTHENTICATED_FULLY]);

    $securityConfig->accessControl()
        ->path('^/api/question/answer')
        ->roles([AuthenticatedVoter::IS_AUTHENTICATED_FULLY]);

    $securityConfig->accessControl()
        ->path('^/')
        ->roles([AuthenticatedVoter::PUBLIC_ACCESS]);
};

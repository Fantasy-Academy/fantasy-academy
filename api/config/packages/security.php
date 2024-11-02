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
        ->pattern('^(/-/health-check|/media/cache|/sitemap)')
        ->stateless(true)
        ->security(false);

    $apiFirewall = $securityConfig->firewall('api')
        ->pattern('^/api')
        ->stateless(true)
        ->lazy(true)
        ->provider('user_provider');

    $mainFirewall = $securityConfig->firewall('main')
        ->lazy(true)
        ->provider('user_provider');

    $securityConfig->accessControl()
        ->path('^/(api/docs)')
        ->roles([AuthenticatedVoter::PUBLIC_ACCESS]);

    $securityConfig->accessControl()
        ->path('^/')
        ->roles([AuthenticatedVoter::IS_AUTHENTICATED_FULLY]);
};

<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Tests;

use ApiPlatform\Symfony\Bundle\Test\Client;
use FantasyAcademy\API\Repository\UserRepository;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;

readonly final class TestingLogin
{
    public static function getJwt(Client $client, string $email): string
    {
        $container = $client->getContainer();

        $repository = $container->get(UserRepository::class);
        $user = $repository->get($email);

        $jwtManager = $container->get(JWTTokenManagerInterface::class);

        return $jwtManager->create($user);
    }
}

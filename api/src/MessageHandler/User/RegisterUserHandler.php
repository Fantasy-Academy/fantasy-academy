<?php

declare(strict_types=1);

namespace FantasyAcademy\API\MessageHandler\User;

use Psr\Clock\ClockInterface;
use Ramsey\Uuid\Uuid;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use FantasyAcademy\API\Entity\User;
use FantasyAcademy\API\Message\User\RegisterUser;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use FantasyAcademy\API\Repository\UserRepository;
use FantasyAcademy\API\Services\ProvideIdentity;

#[AsMessageHandler]
readonly final class RegisterUserHandler
{
    public function __construct(
        private TokenStorageInterface $tokenStorage,
        private UserPasswordHasherInterface $passwordHasher,
        private UserRepository $userRepository,
        private ProvideIdentity $provideIdentity,
        private ClockInterface $clock,
    ) {
    }

    public function __invoke(RegisterUser $message): void
    {
        $user = new User(
            $this->provideIdentity->next(),
            $message->email,
            $this->clock->now(),
            roles: $message->roles,
        );

        $hashedPassword = $this->passwordHasher->hashPassword($user, $message->plainTextPassword);

        $user->changePassword($hashedPassword);


        $this->userRepository->add($user);

        $token = new UsernamePasswordToken($user, 'main', $user->getRoles());
        $this->tokenStorage->setToken($token);
    }
}

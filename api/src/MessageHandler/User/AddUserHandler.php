<?php

declare(strict_types=1);

namespace FantasyAcademy\API\MessageHandler\User;

use Psr\Clock\ClockInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use FantasyAcademy\API\Entity\User;
use FantasyAcademy\API\Message\User\AddUser;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use FantasyAcademy\API\Repository\UserRepository;
use FantasyAcademy\API\Services\ProvideIdentity;

#[AsMessageHandler]
readonly final class AddUserHandler
{
    public function __construct(
        private UserPasswordHasherInterface $passwordHasher,
        private UserRepository $userRepository,
        private ProvideIdentity $provideIdentity,
        private ClockInterface $clock,
    ) {
    }

    public function __invoke(AddUser $message): void
    {
        $user = new User(
            $this->provideIdentity->next(),
            $message->email,
            $this->clock->now(),
            roles: [$message->role],
        );

        $hashedPassword = $this->passwordHasher->hashPassword($user, $message->plainTextPassword);

        $user->changePassword($hashedPassword);

        $this->userRepository->add($user);
    }
}

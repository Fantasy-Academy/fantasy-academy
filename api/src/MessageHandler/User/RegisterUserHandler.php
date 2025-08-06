<?php

declare(strict_types=1);

namespace FantasyAcademy\API\MessageHandler\User;

use FantasyAcademy\API\Exceptions\UserAlreadyRegistered;
use FantasyAcademy\API\Exceptions\UserNotFound;
use Psr\Clock\ClockInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use FantasyAcademy\API\Entity\User;
use FantasyAcademy\API\Message\User\RegisterUser;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use FantasyAcademy\API\Repository\UserRepository;
use FantasyAcademy\API\Services\ProvideIdentity;

#[AsMessageHandler]
readonly final class RegisterUserHandler
{
    public function __construct(
        private UserPasswordHasherInterface $passwordHasher,
        private UserRepository $userRepository,
        private ProvideIdentity $provideIdentity,
        private ClockInterface $clock,
    ) {
    }

    public function __invoke(RegisterUser $message): void
    {
        try {
            $this->userRepository->get($message->email);

            throw new UserAlreadyRegistered();
        } catch (UserNotFound) {
            // Totally ok... continue
        }

        $user = new User(
            $this->provideIdentity->next(),
            $message->email,
            $this->clock->now(),
            name: $message->name,
            roles: [User::ROLE_USER],
        );

        $hashedPassword = $this->passwordHasher->hashPassword($user, $message->password);

        $user->changePassword($hashedPassword);

        $this->userRepository->add($user);
    }
}

<?php

declare(strict_types=1);

namespace FantasyAcademy\API\MessageHandler\User;

use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use FantasyAcademy\API\Message\User\ChangePassword;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use FantasyAcademy\API\Repository\UserRepository;

#[AsMessageHandler]
readonly final class ChangePasswordHandler
{
    public function __construct(
        private UserPasswordHasherInterface $passwordHasher,
        private UserRepository $userRepository,
    ) {
    }

    public function __invoke(ChangePassword $message): void
    {
        $user = $this->userRepository->getById($message->userId());

        $hashedPassword = $this->passwordHasher->hashPassword($user, $message->newPassword);

        $user->changePassword($hashedPassword);
    }
}

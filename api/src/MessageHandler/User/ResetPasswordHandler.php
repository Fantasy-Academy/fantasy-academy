<?php

declare(strict_types=1);

namespace FantasyAcademy\API\MessageHandler\User;

use FantasyAcademy\API\Exceptions\InvalidPasswordResetToken;
use FantasyAcademy\API\Exceptions\PasswordResetTokenNotFound;
use FantasyAcademy\API\Message\User\ResetPassword;
use FantasyAcademy\API\Repository\PasswordResetTokenRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsMessageHandler]
readonly final class ResetPasswordHandler
{
    public function __construct(
        private PasswordResetTokenRepository $passwordResetTokenRepository,
        private UserPasswordHasherInterface $passwordHasher,

    ) {
    }

    /**
     * @throws InvalidPasswordResetToken
     */
    public function __invoke(ResetPassword $message): void
    {
        try {
            $token = $this->passwordResetTokenRepository->get($message->code);
        } catch (PasswordResetTokenNotFound $exception) {
            throw new InvalidPasswordResetToken(previous: $exception);
        }

        $user = $token->user;
        $hashedPassword = $this->passwordHasher->hashPassword($user, $message->newPassword);

        $user->changePassword($hashedPassword);

        // TODO: send email
    }
}

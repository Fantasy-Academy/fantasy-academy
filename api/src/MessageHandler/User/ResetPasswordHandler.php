<?php

declare(strict_types=1);

namespace FantasyAcademy\API\MessageHandler\User;

use Psr\Clock\ClockInterface;
use FantasyAcademy\API\Exceptions\InvalidPasswordResetToken;
use FantasyAcademy\API\Exceptions\PasswordResetTokenExpired;
use FantasyAcademy\API\Exceptions\PasswordResetTokenNotFound;
use FantasyAcademy\API\Message\User\ResetPassword;
use FantasyAcademy\API\Repository\PasswordResetTokenRepository;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsMessageHandler]
readonly final class ResetPasswordHandler
{
    public function __construct(
        private PasswordResetTokenRepository $passwordResetTokenRepository,
        private UserPasswordHasherInterface $passwordHasher,
        private ClockInterface $clock,
        private MailerInterface $mailer,
    ) {
    }

    /**
     * @throws InvalidPasswordResetToken
     * @throws PasswordResetTokenExpired
     */
    public function __invoke(ResetPassword $message): void
    {
        try {
            $token = $this->passwordResetTokenRepository->get($message->code);
        } catch (PasswordResetTokenNotFound $exception) {
            throw new InvalidPasswordResetToken(previous: $exception);
        }

        $now = $this->clock->now();

        if ($token->usedAt !== null) {
            throw new InvalidPasswordResetToken();
        }

        if ($now > $token->validUntil) {
            throw new PasswordResetTokenExpired();
        }

        $user = $token->user;
        $hashedPassword = $this->passwordHasher->hashPassword($user, $message->newPassword);

        $user->changePassword($hashedPassword);
        $token->use($now);

        $email = new TemplatedEmail()
            ->to($user->email)
            ->subject('Password Changed Successfully - Fantasy Academy')
            ->htmlTemplate('emails/password_reset_confirmation.html.twig')
            ->context([
                'userName' => $user->name,
                'changedAt' => $now,
            ]);

        $this->mailer->send($email);
    }
}

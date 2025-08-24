<?php

declare(strict_types=1);

namespace FantasyAcademy\API\MessageHandler\User;

use Psr\Clock\ClockInterface;
use FantasyAcademy\API\Entity\PasswordResetToken;
use FantasyAcademy\API\Exceptions\UserNotFound;
use FantasyAcademy\API\Exceptions\UserNotRegistered;
use FantasyAcademy\API\Message\User\RequestPasswordReset;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use FantasyAcademy\API\Repository\PasswordResetTokenRepository;
use FantasyAcademy\API\Repository\UserRepository;
use FantasyAcademy\API\Services\ProvideIdentity;

#[AsMessageHandler]
readonly final class RequestPasswordResetHandler
{
    public function __construct(
        private UserRepository $userRepository,
        private ProvideIdentity $provideIdentity,
        private PasswordResetTokenRepository $passwordResetTokenRepository,
        private ClockInterface $clock,
        private MailerInterface $mailer,
        #[Autowire(env: 'FRONTEND_URI')]
        private string $frontendUri,
    ) {
    }

    /**
     * @throws UserNotFound
     */
    public function __invoke(RequestPasswordReset $message): void
    {
        $user = $this->userRepository->get($message->email);

        $token = new PasswordResetToken(
            $this->provideIdentity->next(),
            $user,
            $this->clock->now(),
            $this->clock->now()->modify('+8 hours'),
        );

        $this->passwordResetTokenRepository->add($token);

        $resetUrl = sprintf(
            '%s/reset-password?code=%s&email=%s',
            $this->frontendUri,
            $token->id->toString(),
            $user->email,
        );

        $email = new TemplatedEmail()
            ->to($user->email)
            ->subject('Password Reset Request - Fantasy Academy')
            ->htmlTemplate('emails/password_reset_request.html.twig')
            ->context([
                'userName' => $user->name,
                'resetUrl' => $resetUrl,
            ]);

        $this->mailer->send($email);
    }
}

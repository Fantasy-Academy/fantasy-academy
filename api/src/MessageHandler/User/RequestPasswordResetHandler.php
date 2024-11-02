<?php

declare(strict_types=1);

namespace FantasyAcademy\API\MessageHandler\User;

use Psr\Clock\ClockInterface;
use FantasyAcademy\API\Entity\PasswordResetToken;
use FantasyAcademy\API\Exceptions\UserNotFound;
use FantasyAcademy\API\Exceptions\UserNotRegistered;
use FantasyAcademy\API\Message\User\RequestPasswordReset;
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
    ) {
    }

    /**
     * @throws UserNotRegistered
     */
    public function __invoke(RequestPasswordReset $message): void
    {
        try {
            $user = $this->userRepository->get($message->email);
        } catch (UserNotFound) {
            throw new UserNotRegistered();
        }

        $token = new PasswordResetToken(
            $this->provideIdentity->next(),
            $user,
            $this->clock->now(),
            $this->clock->now()->modify('+8 hours'),
        );

        $this->passwordResetTokenRepository->add($token);

        // TODO: send email
    }
}

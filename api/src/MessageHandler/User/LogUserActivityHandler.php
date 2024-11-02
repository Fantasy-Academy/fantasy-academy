<?php

declare(strict_types=1);

namespace FantasyAcademy\API\MessageHandler\User;

use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use FantasyAcademy\API\Exceptions\UserNotFound;
use FantasyAcademy\API\Message\User\LogUserActivity;
use FantasyAcademy\API\Repository\UserRepository;

#[AsMessageHandler]
readonly final class LogUserActivityHandler
{
    public function __construct(
        private UserRepository $userRepository,
    ) {
    }

    public function __invoke(LogUserActivity $message): void
    {
        try {
            $user = $this->userRepository->getById($message->userId);

            if ($message->time->getTimestamp() > (($user->lastActivity?->getTimestamp() ?? 0) + 30)) {
                $user->refreshLastActivity($message->time);
            }
        } catch (UserNotFound) {
            // ... do nothing
        }
    }
}

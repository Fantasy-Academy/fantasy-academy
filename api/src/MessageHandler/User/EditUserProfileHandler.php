<?php

declare(strict_types=1);

namespace FantasyAcademy\API\MessageHandler\User;

use FantasyAcademy\API\Exceptions\UserNotFound;
use FantasyAcademy\API\Message\User\EditUserProfile;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use FantasyAcademy\API\Repository\UserRepository;

#[AsMessageHandler]
readonly final class EditUserProfileHandler
{
    public function __construct(
        private UserRepository $userRepository,
    ) {
    }

    /**
     * @throws UserNotFound
     */
    public function __invoke(EditUserProfile $message): void
    {
        $user = $this->userRepository->getById($message->userId());

        $user->editProfile(
            $message->name,
        );
    }
}

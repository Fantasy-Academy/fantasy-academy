<?php

declare(strict_types=1);

namespace FantasyAcademy\API\MessageHandler\Gameweek;

use FantasyAcademy\API\Message\Gameweek\DeleteGameweek;
use FantasyAcademy\API\Repository\GameweekRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
readonly final class DeleteGameweekHandler
{
    public function __construct(
        private GameweekRepository $gameweekRepository,
    ) {
    }

    public function __invoke(DeleteGameweek $message): void
    {
        $gameweek = $this->gameweekRepository->get($message->id);

        $this->gameweekRepository->remove($gameweek);
    }
}

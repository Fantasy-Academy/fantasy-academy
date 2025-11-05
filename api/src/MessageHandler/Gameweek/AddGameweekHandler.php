<?php

declare(strict_types=1);

namespace FantasyAcademy\API\MessageHandler\Gameweek;

use FantasyAcademy\API\Entity\Gameweek;
use FantasyAcademy\API\Message\Gameweek\AddGameweek;
use FantasyAcademy\API\Repository\GameweekRepository;
use FantasyAcademy\API\Services\ProvideIdentity;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
readonly final class AddGameweekHandler
{
    public function __construct(
        private GameweekRepository $gameweekRepository,
        private ProvideIdentity $provideIdentity,
    ) {
    }

    public function __invoke(AddGameweek $message): void
    {
        $gameweek = new Gameweek(
            $this->provideIdentity->next(),
            $message->season,
            $message->number,
            $message->title,
            $message->description,
            $message->startsAt,
            $message->endsAt,
        );

        $this->gameweekRepository->add($gameweek);
    }
}

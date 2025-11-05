<?php

declare(strict_types=1);

namespace FantasyAcademy\API\MessageHandler\Gameweek;

use FantasyAcademy\API\Entity\Gameweek;
use FantasyAcademy\API\Message\Gameweek\EditGameweek;
use FantasyAcademy\API\Repository\GameweekRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
readonly final class EditGameweekHandler
{
    public function __construct(
        private GameweekRepository $gameweekRepository,
    ) {
    }

    public function __invoke(EditGameweek $message): void
    {
        $existingGameweek = $this->gameweekRepository->get($message->id);

        // Remove and flush the old entity
        $this->gameweekRepository->remove($existingGameweek);
        $this->gameweekRepository->flush();

        // Create new entity with the same ID but updated properties
        $gameweek = new Gameweek(
            $message->id,
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

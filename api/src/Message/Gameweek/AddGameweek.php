<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Message\Gameweek;

use DateTimeImmutable;

readonly final class AddGameweek
{
    public function __construct(
        public int $season,
        public int $number,
        public null|string $title,
        public null|string $description,
        public DateTimeImmutable $startsAt,
        public DateTimeImmutable $endsAt,
    ) {
    }
}

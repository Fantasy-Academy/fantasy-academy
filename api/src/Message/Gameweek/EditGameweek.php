<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Message\Gameweek;

use DateTimeImmutable;
use Symfony\Component\Uid\Uuid;

readonly final class EditGameweek
{
    public function __construct(
        public Uuid $id,
        public int $season,
        public int $number,
        public null|string $title,
        public null|string $description,
        public DateTimeImmutable $startsAt,
        public DateTimeImmutable $endsAt,
    ) {
    }
}

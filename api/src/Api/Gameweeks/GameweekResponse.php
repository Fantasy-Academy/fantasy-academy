<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Api\Gameweeks;

use DateTimeImmutable;
use Symfony\Component\Uid\Uuid;

/**
 * @phpstan-type GameweekResponseRow array{
 *     id: string,
 *     season: int,
 *     number: int,
 *     title: null|string,
 *     description: null|string,
 *     starts_at: string,
 *     ends_at: string,
 * }
 */
readonly final class GameweekResponse
{
    public function __construct(
        public Uuid $id,
        public int $season,
        public int $number,
        public null|string $title,
        public null|string $description,
        public DateTimeImmutable $startsAt,
        public DateTimeImmutable $endsAt,
        public bool $isStarted,
        public bool $isEnded,
        public bool $isCurrent,
    ) {
    }

    /**
     * @param GameweekResponseRow $row
     */
    public static function fromArray(array $row, DateTimeImmutable $now): self
    {
        $startsAt = new DateTimeImmutable($row['starts_at']);
        $endsAt = new DateTimeImmutable($row['ends_at']);

        $nowTimestamp = $now->getTimestamp();
        $isStarted = $nowTimestamp >= $startsAt->getTimestamp();
        $isEnded = $nowTimestamp > $endsAt->getTimestamp();
        $isCurrent = $isStarted && !$isEnded;

        return new self(
            id: Uuid::fromString($row['id']),
            season: $row['season'],
            number: $row['number'],
            title: $row['title'],
            description: $row['description'],
            startsAt: $startsAt,
            endsAt: $endsAt,
            isStarted: $isStarted,
            isEnded: $isEnded,
            isCurrent: $isCurrent,
        );
    }
}

<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Result;

use DateTimeImmutable;

/**
 * @phpstan-type GameweekRowArray array{
 *     id: string,
 *     season: int,
 *     number: int,
 *     title: null|string,
 *     description: null|string,
 *     starts_at: string,
 *     ends_at: string,
 * }
 */
readonly final class GameweekRow
{
    public function __construct(
        public string $id,
        public int $season,
        public int $number,
        public null|string $title,
        public null|string $description,
        public DateTimeImmutable $startsAt,
        public DateTimeImmutable $endsAt,
    ) {
    }

    /**
     * @param GameweekRowArray $data
     */
    public static function createFromArray(array $data): self
    {
        return new self(
            id: $data['id'],
            season: $data['season'],
            number: $data['number'],
            title: $data['title'],
            description: $data['description'],
            startsAt: new DateTimeImmutable($data['starts_at']),
            endsAt: new DateTimeImmutable($data['ends_at']),
        );
    }
}

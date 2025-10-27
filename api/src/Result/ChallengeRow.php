<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Result;

use DateTimeImmutable;

/**
 * @phpstan-type ChallengeRowArray array{
 *     id: string,
 *     name: string,
 *     starts_at: string,
 *     expires_at: string,
 *     evaluated_at: null|string,
 *     gameweek: null|int,
 * }
 */
readonly final class ChallengeRow
{
    public function __construct(
        public string $id,
        public string $name,
        public DateTimeImmutable $startsAt,
        public DateTimeImmutable $expiresAt,
        public null|DateTimeImmutable $evaluatedAt,
        public null|int $gameweek,
    ) {
    }

    /**
     * @param ChallengeRowArray $data
     */
    public static function createFromArray(array $data): self
    {
        return new self(
            id: $data['id'],
            name: $data['name'],
            startsAt: new DateTimeImmutable($data['starts_at']),
            expiresAt: new DateTimeImmutable($data['expires_at']),
            evaluatedAt: $data['evaluated_at'] !== null ? new DateTimeImmutable($data['evaluated_at']) : null,
            gameweek: $data['gameweek'],
        );
    }
}

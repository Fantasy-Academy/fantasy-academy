<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Api\Challenges;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use DateTimeImmutable;
use Symfony\Component\Uid\Uuid;

/**
 * @phpstan-type ChallengeResponseRow array{
 *     id: string,
 *     name: string,
 *     short_description: string,
 *     description: string,
 *     image: null|string,
 *     added_at: string,
 *     starts_at: string,
 *     expires_at: string,
 *     max_points: int,
 *     evaluated_at: null|string,
 *     answered_at: null|string,
 *     my_points: null|int,
 *     gameweek: null|int,
 * }
 */
#[ApiResource(
    shortName: 'Challenges',
)]
#[GetCollection(
    uriTemplate: '/challenges',
    provider: ChallengesProvider::class,
)]
readonly final class ChallengeResponse
{
    public function __construct(
        public Uuid $id,
        public string $name,
        public string $shortDescription,
        public string $description,
        public int $maxPoints,
        public null|string $image,
        public DateTimeImmutable $addedAt,
        public DateTimeImmutable $startsAt,
        public DateTimeImmutable $expiresAt,
        public null|DateTimeImmutable $answeredAt,
        public bool $isStarted,
        public bool $isExpired,
        public bool $isAnswered,
        public bool $isEvaluated,
        public null|int $myPoints,
        public null|int $gameweek,
    ) {
    }

    /**
     * @param ChallengeResponseRow $row
     */
    public static function fromArray(array $row, DateTimeImmutable $now): self
    {
        $answeredAt = $row['answered_at'] !== null ? new DateTimeImmutable($row['answered_at']) : null;
        $startsAt = new DateTimeImmutable($row['starts_at']);
        $expiresAt = new DateTimeImmutable($row['expires_at']);

        return new self(
            id: Uuid::fromString($row['id']),
            name: $row['name'],
            shortDescription: $row['short_description'],
            description: $row['description'],
            maxPoints: $row['max_points'],
            image: $row['image'],
            addedAt: new DateTimeImmutable($row['added_at']),
            startsAt: $startsAt,
            expiresAt: $expiresAt,
            answeredAt: $answeredAt,
            isStarted: $now->getTimestamp() > $startsAt->getTimestamp(),
            isExpired: $now->getTimestamp() > $expiresAt->getTimestamp(),
            isAnswered: $answeredAt !== null,
            isEvaluated: $row['evaluated_at'] !== null,
            myPoints: $row['my_points'],
            gameweek: $row['gameweek'],
        );
    }
}

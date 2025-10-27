<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Api\ChallengeDetail;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use DateTimeImmutable;
use Symfony\Component\Uid\Uuid;

/**
 * @phpstan-type ChallengeDetailResponseRow array{
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
 *     hint_text: null|string,
 *     hint_image: null|string,
 *     my_points: null|int,
 *     show_statistics_continuously: bool,
 *     gameweek: null|int,
 * }
 */
#[ApiResource(
    shortName: 'Challenge detail',
)]
#[Get(
    uriTemplate: '/challenges/{id}',
    provider: ChallengeDetailProvider::class,
)]
readonly final class ChallengeDetailResponse
{
    /**
     * @param array<QuestionResponse> $questions
     */
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
        public array $questions,
        public null|string $hintText,
        public null|string $hintImage,
        public null|int $myPoints,
        public null|int $gameweek,
    ) {
    }

    /**
     * @param ChallengeDetailResponseRow $row
     * @param array<QuestionResponse> $questions
     */
    public static function fromArray(array $row, DateTimeImmutable $now, array $questions): self
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
            questions: $questions,
            hintText: $row['hint_text'],
            hintImage: $row['hint_image'],
            myPoints: $row['my_points'],
            gameweek: $row['gameweek'],
        );
    }
}

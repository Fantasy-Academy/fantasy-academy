<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Api\Response;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use DateTimeImmutable;
use FantasyAcademy\API\Api\StateProvider\ChallengeDetailProvider;
use FantasyAcademy\API\Value\Question;
use Symfony\Component\Uid\Uuid;

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
     * @param array<Question> $questions
     */
    public function __construct(
        public Uuid $id,
        public string $name,
        public string $shortDescription,
        public string $description,
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
    ) {
    }
}

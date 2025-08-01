<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Api\Response;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use DateTimeImmutable;
use FantasyAcademy\API\Api\StateProvider\ChallengesProvider;
use Symfony\Component\Uid\Uuid;

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
        public null|string $image,
        public DateTimeImmutable $addedAt,
        public DateTimeImmutable $startsAt,
        public DateTimeImmutable $expiresAt,
        public null|DateTimeImmutable $answeredAt,
        public bool $isStarted,
        public bool $isExpired,
        public bool $isAnswered,
        public bool $isEvaluated,
    ) {
    }
}

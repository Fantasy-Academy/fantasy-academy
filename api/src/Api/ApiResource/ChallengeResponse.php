<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Api\ApiResource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use DateTimeImmutable;
use FantasyAcademy\API\Api\StateProvider\ChallengesProvider;

#[ApiResource]
#[GetCollection(
    uriTemplate: '/challenges',
    provider: ChallengesProvider::class,
)]
readonly final class ChallengeResponse
{
    public function __construct(
        public string $name,
        public DateTimeImmutable $addedAt,
        public DateTimeImmutable $expiresAt,
        public null|DateTimeImmutable $completedAt,
    ) {
    }
}

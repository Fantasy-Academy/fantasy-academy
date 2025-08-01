<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Api\ApiResource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use DateTimeImmutable;
use FantasyAcademy\API\Api\StateProvider\LoggedUserProvider;

#[ApiResource(
    shortName: 'Logged user info',
    security: "is_granted('IS_AUTHENTICATED_FULLY')",
)]
#[Get(
    uriTemplate: '/me',
    provider: LoggedUserProvider::class,
)]
final class LoggedUserResponse
{
    /**
     * @param array<PlayerSeasonStatistics> $seasonsStatistics
     */
    public function __construct(
        public string $id,
        public string $name,
        public string $email,
        public int $availableChallenges,
        public int $completedChallenges,
        public DateTimeImmutable $registeredAt,
        public PlayerStatistics $overallStatistics,
        public array $seasonsStatistics,
    ) {
    }
}

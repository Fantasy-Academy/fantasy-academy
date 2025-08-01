<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Api\Response;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use DateTimeImmutable;
use FantasyAcademy\API\Api\StateProvider\LoggedUserProvider;
use FantasyAcademy\API\Value\PlayerSeasonStatistics;
use FantasyAcademy\API\Value\PlayerStatistics;

#[ApiResource(
    shortName: 'Logged user info',
)]
#[Get(
    uriTemplate: '/me',
    security: "is_granted('IS_AUTHENTICATED_FULLY')",
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

<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Api\LoggedUser;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use DateTimeImmutable;
use FantasyAcademy\API\Value\PlayerSeasonStatistics;
use FantasyAcademy\API\Value\PlayerSkill;
use FantasyAcademy\API\Value\PlayerStatistics;
use Symfony\Component\Uid\Uuid;

/**
 * @phpstan-type LoggedUserResponseRow array{
 *     id: string,
 *     name: null|string,
 *     email: string,
 *     registered_at: string,
 *     points: int,
 *     challenges_answered: int,
 *     rank: null|int,
 * }
 */
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
        public Uuid $id,
        public string $name,
        public string $email,
        public DateTimeImmutable $registeredAt,
        public int $availableChallenges,
        public PlayerStatistics $overallStatistics,
        public array $seasonsStatistics,
    ) {
    }

    /**
     * @param LoggedUserResponseRow $data
     * @param array<PlayerSkill> $skills
     */
    public static function fromArray(array $data, int $availableChallenges, array $skills): self
    {
        return new self(
            id: Uuid::fromString($data['id']),
            name: $data['name'] ?? '-',
            email: $data['email'],
            registeredAt: new DateTimeImmutable($data['registered_at']),
            availableChallenges: $availableChallenges,
            overallStatistics: new PlayerStatistics(
                rank: $data['rank'],
                challengesAnswered: $data['challenges_answered'],
                points: $data['points'],
                skills: $skills,
            ),
            seasonsStatistics: [
                new PlayerSeasonStatistics(
                    seasonNumber: 1,
                    rank: $data['rank'],
                    challengesAnswered: $data['challenges_answered'],
                    points: $data['points'],
                    skills: [],
                ),
            ],
        );
    }
}

<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Api\Response;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use DateTimeImmutable;
use FantasyAcademy\API\Api\StateProvider\PlayerInfoProvider;
use FantasyAcademy\API\Value\PlayerSeasonStatistics;
use FantasyAcademy\API\Value\PlayerStatistics;
use Symfony\Component\Uid\Uuid;

/**
 * @phpstan-type PlayerInfoResponseRow array{
 *     id: string,
 *     name: null|string,
 *     registered_at: string,
 *     points: int,
 *     challenges_answered: int,
 *     rank: null|int,
 * }
 */
#[ApiResource(
    shortName: 'Player info',
)]
#[Get(
    uriTemplate: '/player/{id}',
    provider: PlayerInfoProvider::class,
)]
readonly final class PlayerInfoResponse
{
    /**
     * @param array<PlayerSeasonStatistics> $seasonsStatistics
     */
    public function __construct(
        public Uuid $id,
        public bool $isMyself,
        public string $name,
        public DateTimeImmutable $registeredAt,
        public PlayerStatistics $overallStatistics,
        public array $seasonsStatistics,
    ) {
    }

    /**
     * @param PlayerInfoResponseRow $data
     */
    public static function fromArray(array $data, null|Uuid $userId): self
    {
        return new self(
            id: Uuid::fromString($data['id']),
            isMyself: $data['id'] === $userId?->toString(),
            name: $data['name'] ?? '-',
            registeredAt: new DateTimeImmutable($data['registered_at']),
            overallStatistics: new PlayerStatistics(
                rank: $data['rank'],
                challengesAnswered: $data['challenges_answered'],
                points: $data['points'],
                skills: [],
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

<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Api\Leaderboards;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use FantasyAcademy\API\Value\PlayerSkill;
use Symfony\Component\Uid\Uuid;

/**
 * @phpstan-type LeaderboardResponseRow array{
 *     player_id: string,
 *     player_name: string,
 *     rank: int,
 *     points: int,
 *     challenges_answered: int,
 *     skills: array<PlayerSkill>,
 *     rank_change: int,
 *     points_change: int
 * }
 */
#[ApiResource(
    shortName: 'Leaderboards',
)]
#[GetCollection(
    uriTemplate: '/leaderboards',
    provider: LeaderboardsProvider::class,
)]
final class LeaderboardResponse
{
    /**
     * @param array<PlayerSkill> $skills
     */
    public function __construct(
        public Uuid $playerId,
        public string $playerName,
        public bool $isMyself,
        public int $rank,
        public int $points,
        public int $challengesAnswered,
        public array $skills,
        public int $rankChange,
        public int $pointsChange,
    ) {
    }

    /**
     * @param LeaderboardResponseRow $data
     */
    public static function fromArray(array $data, null|Uuid $userId): self
    {
        return new self(
            playerId: Uuid::fromString($data['player_id']),
            playerName: $data['player_name'],
            isMyself: $data['player_id'] === $userId?->toString(),
            rank: $data['rank'],
            points: $data['points'],
            challengesAnswered: $data['challenges_answered'],
            skills: [], // TODO
            rankChange: $data['rank_change'],
            pointsChange: $data['points_change'],
        );
    }
}

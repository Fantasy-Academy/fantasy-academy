<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Api\Leaderboards;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use Doctrine\DBAL\Connection;
use FantasyAcademy\API\Entity\User;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Uid\Uuid;

/**
 * @phpstan-import-type LeaderboardResponseRow from LeaderboardResponse
 *
 * @implements ProviderInterface<LeaderboardResponse>
 */
readonly final class LeaderboardsProvider implements ProviderInterface
{
    public function __construct(
        private Security $security,
        private Connection $database,
    ) {}

    /**
     * @return array<LeaderboardResponse>
     */
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): array
    {
        /** @var null|User $user */
        $user = $this->security->getUser();

        return $this->getLeaderboards($user?->id);
    }

    /**
     * @return array<LeaderboardResponse>
     */
    private function getLeaderboards(null|Uuid $userId): array
    {
        $query = <<<SQL
WITH latest_gameweek AS (
  SELECT MAX(gameweek) AS latest_gw
  FROM challenge
  WHERE evaluated_at IS NOT NULL
    AND gameweek IS NOT NULL
),
previous_week_stats AS (
  SELECT
    u.id AS user_id,
    COALESCE(SUM(CASE
      WHEN c.evaluated_at IS NOT NULL
        AND (c.gameweek IS NULL OR c.gameweek < (SELECT latest_gw FROM latest_gameweek))
      THEN pca.points
      ELSE 0
    END), 0) AS points,
    ROW_NUMBER() OVER (
      ORDER BY COALESCE(SUM(CASE
        WHEN c.evaluated_at IS NOT NULL
          AND (c.gameweek IS NULL OR c.gameweek < (SELECT latest_gw FROM latest_gameweek))
        THEN pca.points
        ELSE 0
      END), 0) DESC, u.name ASC
    ) AS rank
  FROM "user" u
  LEFT JOIN player_challenge_answer pca ON pca.user_id = u.id
  LEFT JOIN challenge c ON c.id = pca.challenge_id
  GROUP BY u.id, u.name
),
current_stats AS (
  SELECT
    u.id AS user_id,
    u.name AS user_name,
    COALESCE(SUM(CASE WHEN c.evaluated_at IS NOT NULL THEN pca.points ELSE 0 END), 0) AS points,
    COUNT(CASE WHEN c.evaluated_at IS NOT NULL THEN pca.id ELSE NULL END) AS challenges_answered,
    ROW_NUMBER() OVER (
      ORDER BY COALESCE(SUM(CASE WHEN c.evaluated_at IS NOT NULL THEN pca.points ELSE 0 END), 0) DESC, u.name ASC
    ) AS rank
  FROM "user" u
  LEFT JOIN player_challenge_answer pca ON pca.user_id = u.id
  LEFT JOIN challenge c ON c.id = pca.challenge_id
  GROUP BY u.id, u.name
)
SELECT
  cs.user_id AS player_id,
  cs.user_name AS player_name,
  cs.rank::int AS rank,
  cs.points::int AS points,
  cs.challenges_answered::int AS challenges_answered,
  (COALESCE(pws.rank, cs.rank) - cs.rank)::int AS rank_change,
  (cs.points - COALESCE(pws.points, 0))::int AS points_change
FROM current_stats cs
LEFT JOIN previous_week_stats pws ON pws.user_id = cs.user_id
ORDER BY cs.rank ASC;
SQL;

        /** @var array<LeaderboardResponseRow> $rows */
        $rows = $this->database
            ->executeQuery($query)
            ->fetchAllAssociative();

        return array_map(
            callback: fn (array $row): LeaderboardResponse => LeaderboardResponse::fromArray($row, $userId),
            array: $rows,
        );
    }
}

<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Query;

use Doctrine\DBAL\Connection;
use FantasyAcademy\API\Exceptions\UserNotFound;

readonly final class UserDisciplineQuery
{
    public function __construct(
        private Connection $connection,
    ) {
    }

    /**
     * @throws UserNotFound
     */
    public function forPlayer(string $playerId): float
    {
        $sql = <<<SQL
SELECT
  CAST(
    ROUND(
      COALESCE(
        (
          (COUNT(DISTINCT pca.challenge_id)
             FILTER (WHERE pca.answered_at IS NOT NULL)
          )::numeric
          / NULLIF(COUNT(DISTINCT c.id), 0)
        ) * 100,
        0
      ),
      1
    ) AS numeric(6,1)
  ) AS discipline_percent
FROM "user" AS u
JOIN challenge AS c
  ON c.expires_at > u.registered_at
LEFT JOIN player_challenge_answer AS pca
  ON pca.user_id = u.id
 AND pca.challenge_id = c.id
WHERE u.id = :userId;
SQL;

        /** @var numeric-string|false $result */
        $result = $this->connection->executeQuery($sql, ['userId' => $playerId])->fetchOne();

        if ($result === false) {
            throw new UserNotFound();
        }

        return (float) $result;
    }

    /**
     * @throws UserNotFound
     * @return array{current: float, previous: null|float}
     */
    public function forPlayerWithPreviousWeek(string $playerId): array
    {
        $sql = <<<SQL
WITH latest_gameweek AS (
  SELECT MAX(gameweek) AS latest_gw
  FROM challenge
  WHERE evaluated_at IS NOT NULL
    AND gameweek IS NOT NULL
),
previous_week_challenges AS (
  SELECT COUNT(DISTINCT c.id) AS total_challenges
  FROM "user" AS u
  JOIN challenge AS c
    ON c.expires_at > u.registered_at
   AND c.evaluated_at IS NOT NULL
   AND (c.gameweek IS NULL OR c.gameweek < (SELECT latest_gw FROM latest_gameweek))
  WHERE u.id = :userId
),
previous_week_answers AS (
  SELECT COUNT(DISTINCT pca.challenge_id) AS answered_challenges
  FROM "user" AS u
  JOIN challenge AS c
    ON c.expires_at > u.registered_at
   AND c.evaluated_at IS NOT NULL
   AND (c.gameweek IS NULL OR c.gameweek < (SELECT latest_gw FROM latest_gameweek))
  LEFT JOIN player_challenge_answer AS pca
    ON pca.user_id = u.id
   AND pca.challenge_id = c.id
   AND pca.answered_at IS NOT NULL
  WHERE u.id = :userId
),
current_challenges AS (
  SELECT COUNT(DISTINCT c.id) AS total_challenges
  FROM "user" AS u
  JOIN challenge AS c
    ON c.expires_at > u.registered_at
   AND c.evaluated_at IS NOT NULL
  WHERE u.id = :userId
),
current_answers AS (
  SELECT COUNT(DISTINCT pca.challenge_id) AS answered_challenges
  FROM "user" AS u
  JOIN challenge AS c
    ON c.expires_at > u.registered_at
   AND c.evaluated_at IS NOT NULL
  LEFT JOIN player_challenge_answer AS pca
    ON pca.user_id = u.id
   AND pca.challenge_id = c.id
   AND pca.answered_at IS NOT NULL
  WHERE u.id = :userId
)
SELECT
  CAST(
    ROUND(
      COALESCE(
        (ca.answered_challenges::numeric / NULLIF(cc.total_challenges, 0)) * 100,
        0
      ),
      1
    ) AS numeric(6,1)
  ) AS discipline_percent_current,
  CASE
    WHEN pwc.total_challenges > 0 THEN
      CAST(
        ROUND(
          COALESCE(
            (pwa.answered_challenges::numeric / NULLIF(pwc.total_challenges, 0)) * 100,
            0
          ),
          1
        ) AS numeric(6,1)
      )
    ELSE NULL
  END AS discipline_percent_previous
FROM current_challenges cc
CROSS JOIN current_answers ca
CROSS JOIN previous_week_challenges pwc
CROSS JOIN previous_week_answers pwa
SQL;

        /** @var array{discipline_percent_current: numeric-string, discipline_percent_previous: numeric-string|null}|false $result */
        $result = $this->connection->executeQuery($sql, [
            'userId' => $playerId,
        ])->fetchAssociative();

        if ($result === false) {
            throw new UserNotFound();
        }

        return [
            'current' => (float) $result['discipline_percent_current'],
            'previous' => $result['discipline_percent_previous'] !== null ? (float) $result['discipline_percent_previous'] : null,
        ];
    }
}

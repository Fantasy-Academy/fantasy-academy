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
}

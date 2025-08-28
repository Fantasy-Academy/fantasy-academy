<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Query;

use Doctrine\DBAL\Connection;
use FantasyAcademy\API\Result\PlayerChallengeAnswerRow;
use Symfony\Component\Uid\Uuid;

/**
 * @phpstan-import-type PlayerChallengeAnswerRowArray from PlayerChallengeAnswerRow
 */
readonly final class PlayerChallengeAnswerQuery
{
    public function __construct(
        private Connection $connection,
    ) {
    }

    /**
     * @return array<PlayerChallengeAnswerRow>
     */
    public function getForChallenge(Uuid $challengeId): array
    {
        $sql = <<<SQL
SELECT
  pca.id,
  pca.user_id,
  pca.challenge_id,
  c.name,
  pca.points
FROM player_challenge_answer AS pca
JOIN challenge AS c
  ON c.id = pca.challenge_id
WHERE c.id = :challengeId
SQL;

        /** @var array<PlayerChallengeAnswerRowArray> $rows */
        $rows = $this->connection->executeQuery($sql, [
            'challengeId' => $challengeId->toString(),
        ])->fetchAllAssociative();

        return array_map(
            callback: fn (array $row): PlayerChallengeAnswerRow => PlayerChallengeAnswerRow::createFromArray($row),
            array: $rows,
        );
    }
}

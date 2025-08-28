<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Query;

use Doctrine\DBAL\Connection;
use FantasyAcademy\API\Result\ChallengeRow;

/**
 * @phpstan-import-type ChallengeRowArray from ChallengeRow
 */
readonly final class ChallengeQuery
{
    public function __construct(
        private Connection $connection,
    ) {
    }

    /**
     * @return array<ChallengeRow>
     */
    public function getAll(): array
    {
        $sql = <<<SQL
SELECT
  c.id,
  c.name,
  c.starts_at,
  c.expires_at,
  c.evaluated_at
FROM challenge AS c
ORDER BY c.starts_at DESC
SQL;

        /** @var array<ChallengeRowArray> $rows */
        $rows = $this->connection->executeQuery($sql)->fetchAllAssociative();

        return array_map(
            callback: fn (array $row): ChallengeRow => ChallengeRow::createFromArray($row),
            array: $rows,
        );
    }
}

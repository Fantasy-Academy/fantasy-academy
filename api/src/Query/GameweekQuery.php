<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Query;

use Doctrine\DBAL\Connection;
use FantasyAcademy\API\Result\GameweekRow;

/**
 * @phpstan-import-type GameweekRowArray from GameweekRow
 */
readonly final class GameweekQuery
{
    public function __construct(
        private Connection $connection,
    ) {
    }

    /**
     * @return array<GameweekRow>
     */
    public function getAll(): array
    {
        $sql = <<<SQL
SELECT
  g.id,
  g.season,
  g.number,
  g.title,
  g.description,
  g.starts_at,
  g.ends_at
FROM gameweek AS g
ORDER BY g.season DESC, g.number DESC
SQL;

        /** @var array<GameweekRowArray> $rows */
        $rows = $this->connection->executeQuery($sql)->fetchAllAssociative();

        return array_map(
            callback: static fn (array $row): GameweekRow => GameweekRow::createFromArray($row),
            array: $rows,
        );
    }

    public function getById(string $id): null|GameweekRow
    {
        $sql = <<<SQL
SELECT
  g.id,
  g.season,
  g.number,
  g.title,
  g.description,
  g.starts_at,
  g.ends_at
FROM gameweek AS g
WHERE g.id = :id
SQL;

        /** @var GameweekRowArray|false $row */
        $row = $this->connection->executeQuery($sql, ['id' => $id])->fetchAssociative();

        if ($row === false) {
            return null;
        }

        return GameweekRow::createFromArray($row);
    }
}

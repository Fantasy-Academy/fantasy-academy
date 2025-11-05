<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Api\Gameweeks;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use Doctrine\DBAL\Connection;
use Psr\Clock\ClockInterface;

/**
 * @phpstan-import-type GameweekResponseRow from GameweekResponse
 *
 * @implements ProviderInterface<GameweeksResponse>
 */
readonly final class GameweeksProvider implements ProviderInterface
{
    public function __construct(
        private Connection $database,
        private ClockInterface $clock,
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object
    {
        $now = $this->clock->now();

        $current = $this->getCurrentGameweek($now);
        $next = $this->getNextGameweek($now);
        $previous = $this->getPreviousGameweek($now);

        return new GameweeksResponse(
            current: $current,
            next: $next,
            previous: $previous,
        );
    }

    private function getCurrentGameweek(\DateTimeImmutable $now): null|GameweekResponse
    {
        $query = <<<SQL
SELECT *
FROM gameweek
WHERE :now BETWEEN starts_at AND ends_at
LIMIT 1
SQL;

        /** @var false|GameweekResponseRow $row */
        $row = $this->database
            ->executeQuery($query, [
                'now' => $now->format('Y-m-d H:i:s'),
            ])
            ->fetchAssociative();

        if ($row === false) {
            return null;
        }

        return GameweekResponse::fromArray($row, $now);
    }

    private function getNextGameweek(\DateTimeImmutable $now): null|GameweekResponse
    {
        $query = <<<SQL
SELECT *
FROM gameweek
WHERE starts_at > :now
ORDER BY starts_at ASC
LIMIT 1
SQL;

        /** @var false|GameweekResponseRow $row */
        $row = $this->database
            ->executeQuery($query, [
                'now' => $now->format('Y-m-d H:i:s'),
            ])
            ->fetchAssociative();

        if ($row === false) {
            return null;
        }

        return GameweekResponse::fromArray($row, $now);
    }

    private function getPreviousGameweek(\DateTimeImmutable $now): null|GameweekResponse
    {
        $query = <<<SQL
SELECT *
FROM gameweek
WHERE ends_at < :now
ORDER BY ends_at DESC
LIMIT 1
SQL;

        /** @var false|GameweekResponseRow $row */
        $row = $this->database
            ->executeQuery($query, [
                'now' => $now->format('Y-m-d H:i:s'),
            ])
            ->fetchAssociative();

        if ($row === false) {
            return null;
        }

        return GameweekResponse::fromArray($row, $now);
    }
}

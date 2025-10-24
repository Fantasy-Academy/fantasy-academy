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
SELECT
  u.id   AS player_id,
  u.name AS player_name,
  COALESCE(SUM(pca.points), 0) AS points,
  COUNT(pca.id) AS challenges_answered,
  ROW_NUMBER() OVER (
    ORDER BY COALESCE(SUM(pca.points), 0) DESC, u.name ASC
  ) AS rank
FROM "user" u
LEFT JOIN player_challenge_answer pca
  ON pca.user_id = u.id
GROUP BY u.id, u.name
ORDER BY points DESC, player_name ASC;
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

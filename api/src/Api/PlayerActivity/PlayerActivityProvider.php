<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Api\PlayerActivity;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use Doctrine\DBAL\Connection;
use Symfony\Component\Uid\Uuid;

/**
 * @phpstan-import-type PlayerActivityRow from PlayerActivityResponse
 *
 * @implements ProviderInterface<PlayerActivityResponse>
 */
readonly final class PlayerActivityProvider implements ProviderInterface
{
    public function __construct(
        private Connection $database,
    ) {}

    /**
     * @param array{id?: Uuid} $uriVariables
     */
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): PlayerActivityResponse
    {
        assert(isset($uriVariables['id']));
        $playerId = $uriVariables['id'];

        return $this->getPlayerActivity($playerId);
    }

    private function getPlayerActivity(Uuid $playerId): PlayerActivityResponse
    {
        $query = <<<SQL
SELECT
  c.gameweek,
  COUNT(DISTINCT c.id) AS total_challenges,
  COUNT(DISTINCT CASE WHEN pca.answered_at IS NOT NULL THEN c.id END) AS answered_challenges,
  COALESCE(SUM(pca.points), 0) AS points_earned,
  SUM(c.max_points) AS max_points_possible
FROM challenge c
LEFT JOIN player_challenge_answer pca
  ON pca.challenge_id = c.id
  AND pca.user_id = :playerId
WHERE c.evaluated_at IS NOT NULL
  AND c.gameweek IS NOT NULL
GROUP BY c.gameweek
ORDER BY c.gameweek ASC
SQL;

        /** @var array<PlayerActivityRow> $rows */
        $rows = $this->database
            ->executeQuery($query, [
                'playerId' => $playerId->toString(),
            ])
            ->fetchAllAssociative();

        return PlayerActivityResponse::fromArray($playerId, $rows);
    }
}

<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Api\PlayerInfo;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use Doctrine\DBAL\Connection;
use FantasyAcademy\API\Entity\User;
use FantasyAcademy\API\Exceptions\UserNotFound;
use FantasyAcademy\API\Query\UserSkillsPercentilesQuery;
use FantasyAcademy\API\Services\SkillsTransformer;
use FantasyAcademy\API\Value\PlayerSkill;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Uid\Uuid;

/**
 * @phpstan-import-type PlayerInfoResponseRow from PlayerInfoResponse
 *
 * @implements ProviderInterface<PlayerInfoResponse>
 */
readonly final class PlayerInfoProvider implements ProviderInterface
{
    public function __construct(
        private Security $security,
        private Connection $database,
        private UserSkillsPercentilesQuery $userSkillsPercentilesQuery,
        private SkillsTransformer $skillsTransformer,
    ) {}

    /**
     * @param array{id?: Uuid} $uriVariables
     */
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): PlayerInfoResponse
    {
        /** @var null|User $user */
        $user = $this->security->getUser();

        assert(isset($uriVariables['id']));
        $playerId = $uriVariables['id'];

        return $this->getPlayerInfo($playerId, $user?->id);
    }

    private function getPlayerInfo(Uuid $playerId, null|Uuid $userId): PlayerInfoResponse
    {
        $query = <<<SQL
WITH agg AS (
  SELECT
    user_id,
    SUM(COALESCE(points, 0)) AS points,
    COUNT(challenge_id) AS challenges_answered
  FROM player_challenge_answer
  GROUP BY user_id
),
ranked AS (
  SELECT
    a.*,
    ROW_NUMBER() OVER (ORDER BY a.points DESC, a.user_id) AS rank
  FROM agg a
)
SELECT
  u.*,
  COALESCE(r.points, 0) AS points,
  COALESCE(r.challenges_answered, 0) AS challenges_answered,
  r.rank
FROM "user" u
LEFT JOIN ranked r ON r.user_id = u.id
WHERE u.id = :playerId;
SQL;

        /** @var false|PlayerInfoResponseRow $row */
        $row = $this->database
            ->executeQuery($query, [
                'playerId' => $playerId->toString(),
            ])
            ->fetchAssociative();

        if ($row === false) {
            throw new UserNotFound();
        }

        $skills = $this->getPlayerSkills($playerId);

        return PlayerInfoResponse::fromArray($row, $userId, $skills);
    }

    /**
     * @return array<PlayerSkill>
     */
    private function getPlayerSkills(Uuid $userId): array
    {
        try {
            $skillsRow = $this->userSkillsPercentilesQuery->forPlayer($userId->toString());

            return $this->skillsTransformer->transformToPlayerSkills($skillsRow, $userId->toString());
        } catch (UserNotFound) {
            return [];
        }
    }
}

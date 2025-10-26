<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Api\PlayerInfo;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use Doctrine\DBAL\Connection;
use FantasyAcademy\API\Entity\User;
use FantasyAcademy\API\Exceptions\UserNotFound;
use FantasyAcademy\API\Query\UserDisciplineQuery;
use FantasyAcademy\API\Query\UserSkillsPercentilesQuery;
use FantasyAcademy\API\Services\GameWeekService;
use FantasyAcademy\API\Services\SkillsTransformer;
use FantasyAcademy\API\Value\PlayerSkill;
use Psr\Clock\ClockInterface;
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
        private UserDisciplineQuery $userDisciplineQuery,
        private SkillsTransformer $skillsTransformer,
        private ClockInterface $clock,
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
        $now = $this->clock->now();
        $lastMondayCutoff = GameWeekService::getLastMondayCutoff($now);

        $query = <<<SQL
WITH previous_week_agg AS (
  SELECT
    user_id,
    SUM(CASE WHEN c.evaluated_at IS NOT NULL AND c.evaluated_at <= :lastMondayCutoff THEN COALESCE(points, 0) ELSE 0 END) AS points
  FROM player_challenge_answer pca
  LEFT JOIN challenge c ON c.id = pca.challenge_id
  GROUP BY user_id
),
previous_week_ranked AS (
  SELECT
    a.*,
    ROW_NUMBER() OVER (ORDER BY a.points DESC, a.user_id) AS rank
  FROM previous_week_agg a
),
current_agg AS (
  SELECT
    user_id,
    SUM(COALESCE(points, 0)) AS points,
    COUNT(challenge_id) AS challenges_answered
  FROM player_challenge_answer
  GROUP BY user_id
),
current_ranked AS (
  SELECT
    a.*,
    ROW_NUMBER() OVER (ORDER BY a.points DESC, a.user_id) AS rank
  FROM current_agg a
)
SELECT
  u.*,
  COALESCE(cr.points, 0) AS points,
  COALESCE(cr.challenges_answered, 0) AS challenges_answered,
  cr.rank,
  COALESCE((COALESCE(pwr.rank, cr.rank) - cr.rank)::int, 0) AS rank_change,
  COALESCE((COALESCE(cr.points, 0) - COALESCE(pwr.points, 0))::int, 0) AS points_change
FROM "user" u
LEFT JOIN current_ranked cr ON cr.user_id = u.id
LEFT JOIN previous_week_ranked pwr ON pwr.user_id = u.id
WHERE u.id = :playerId;
SQL;

        /** @var false|PlayerInfoResponseRow $row */
        $row = $this->database
            ->executeQuery($query, [
                'playerId' => $playerId->toString(),
                'lastMondayCutoff' => $lastMondayCutoff->format('Y-m-d H:i:s'),
            ])
            ->fetchAssociative();

        if ($row === false) {
            throw new UserNotFound();
        }

        $skills = $this->getPlayerSkills($playerId, $lastMondayCutoff);

        return PlayerInfoResponse::fromArray($row, $userId, $skills);
    }

    /**
     * @return array<PlayerSkill>
     */
    private function getPlayerSkills(Uuid $userId, \DateTimeImmutable $cutoff): array
    {
        try {
            $skillsRow = $this->userSkillsPercentilesQuery->forPlayerWithPreviousWeek($userId->toString(), $cutoff);
            $disciplineData = $this->userDisciplineQuery->forPlayerWithPreviousWeek($userId->toString(), $cutoff);

            return $this->skillsTransformer->transformToPlayerSkillsWithChange($skillsRow, $userId->toString(), $disciplineData);
        } catch (UserNotFound) {
            return [];
        }
    }
}

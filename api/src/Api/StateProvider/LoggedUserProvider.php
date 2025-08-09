<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Api\StateProvider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use Doctrine\DBAL\Connection;
use FantasyAcademy\API\Api\Response\LoggedUserResponse;
use FantasyAcademy\API\Entity\User;
use FantasyAcademy\API\Exceptions\UserNotFound;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Uid\Uuid;

/**
 * @phpstan-import-type LoggedUserResponseRow from LoggedUserResponse
 *
 * @implements ProviderInterface<LoggedUserResponse>
 */
readonly final class LoggedUserProvider implements ProviderInterface
{
    public function __construct(
        private Security $security,
        private Connection $database,
    ) {}

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object
    {
        $user = $this->security->getUser();
        assert($user instanceof User);

        return $this->getPlayerInfo($user->id);
    }

    private function getPlayerInfo(Uuid $userId): LoggedUserResponse
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

        /** @var false|LoggedUserResponseRow $row */
        $row = $this->database
            ->executeQuery($query, [
                'playerId' => $userId->toString(),
            ])
            ->fetchAssociative();

        if ($row === false) {
            throw new UserNotFound();
        }

        $availableChallengesCount = $this->getAvailableChallengesCount($userId);

        return LoggedUserResponse::fromArray($row, $availableChallengesCount);
    }

    private function getAvailableChallengesCount(Uuid $userId): int
    {
        $query = <<<SQL
SELECT COUNT(*) AS available_challenges
FROM challenge c
WHERE NOT EXISTS (
  SELECT 1
  FROM player_challenge_answer pca
  WHERE pca.user_id = :userId
    AND pca.challenge_id = c.id
    AND pca.answered_at IS NOT NULL
);
SQL;

        /** @var int $result */
        $result = $this->database
            ->executeQuery($query, [
                'userId' => $userId->toString(),
            ])
            ->fetchOne();

        return $result;
    }
}

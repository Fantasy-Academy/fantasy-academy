<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Api\Challenges;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use Doctrine\DBAL\Connection;
use FantasyAcademy\API\Entity\User;
use Psr\Clock\ClockInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Uid\Uuid;

/**
 * @phpstan-import-type ChallengeResponseRow from ChallengeResponse
 *
 * @implements ProviderInterface<ChallengeResponse>
 */
readonly final class ChallengesProvider implements ProviderInterface
{
    public function __construct(
        private Security $security,
        private Connection $database,
        private ClockInterface $clock,
    ) {}

    /**
     * @return array<ChallengeResponse>
     */
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): array
    {
        /** @var null|User $user */
        $user = $this->security->getUser();

        return $this->getChallenges($user?->id);
    }

    /**
     * @return array<ChallengeResponse>
     */
    private function getChallenges(null|Uuid $userId): array
    {
        $query = <<<SQL
SELECT challenge.*, player_challenge_answer.answered_at, player_challenge_answer.points AS my_points,
    CASE
        WHEN challenge.evaluated_at IS NOT NULL AND player_challenge_answer.points IS NOT NULL THEN
            (SELECT COUNT(DISTINCT pca2.user_id) + 1
             FROM player_challenge_answer pca2
             WHERE pca2.challenge_id = challenge.id
               AND pca2.points > player_challenge_answer.points)
        ELSE NULL
    END AS my_rank,
    CASE
        WHEN challenge.evaluated_at IS NOT NULL THEN
            (SELECT COUNT(DISTINCT pca3.user_id)
             FROM player_challenge_answer pca3
             WHERE pca3.challenge_id = challenge.id)
        ELSE NULL
    END AS total_players
FROM challenge
LEFT JOIN player_challenge_answer ON challenge.id = player_challenge_answer.challenge_id AND player_challenge_answer.user_id = :userId
ORDER BY challenge.expires_at DESC
SQL;

        /** @var array<ChallengeResponseRow> $rows */
        $rows = $this->database
            ->executeQuery($query, [
                'userId' => $userId?->toString(),
            ])
            ->fetchAllAssociative();

        return array_map(
            callback: fn (array $row): ChallengeResponse => ChallengeResponse::fromArray($row, $this->clock->now()),
            array: $rows,
        );
    }
}

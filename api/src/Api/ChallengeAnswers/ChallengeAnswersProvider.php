<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Api\ChallengeAnswers;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use Doctrine\DBAL\Connection;
use FantasyAcademy\API\Entity\User;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Uid\Uuid;

/**
 * @phpstan-import-type PlayerAnswerRow from ChallengeAnswersResponse
 *
 * @implements ProviderInterface<ChallengeAnswersResponse>
 */
readonly final class ChallengeAnswersProvider implements ProviderInterface
{
    public function __construct(
        private Security $security,
        private Connection $database,
    ) {}

    /**
     * @param array{id?: Uuid} $uriVariables
     */
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): ChallengeAnswersResponse
    {
        /** @var null|User $user */
        $user = $this->security->getUser();

        assert(isset($uriVariables['id']));
        $challengeId = $uriVariables['id'];

        return $this->getChallengeAnswers($challengeId, $user?->id);
    }

    private function getChallengeAnswers(Uuid $challengeId, null|Uuid $userId): ChallengeAnswersResponse
    {
        // First check if challenge is evaluated
        $evaluationQuery = <<<SQL
SELECT evaluated_at
FROM challenge
WHERE id = :challengeId
SQL;

        /** @var false|array{evaluated_at: null|string} $evaluationRow */
        $evaluationRow = $this->database
            ->executeQuery($evaluationQuery, [
                'challengeId' => $challengeId->toString(),
            ])
            ->fetchAssociative();

        // If challenge doesn't exist or is not evaluated, return empty players array
        if ($evaluationRow === false || $evaluationRow['evaluated_at'] === null) {
            return new ChallengeAnswersResponse(
                id: $challengeId,
                players: [],
            );
        }

        // Get all player answers for this challenge
        $query = <<<SQL
SELECT
    pca.user_id,
    u.name AS user_name,
    pca.points,
    paq.question_id,
    q.text AS question_text,
    paq.text_answer,
    paq.numeric_answer,
    paq.selected_choice_id,
    paq.selected_choice_ids,
    paq.ordered_choice_ids,
    q.choice_constraint
FROM player_challenge_answer pca
JOIN "user" u ON u.id = pca.user_id
JOIN player_answered_question paq ON paq.challenge_answer_id = pca.id
JOIN question q ON q.id = paq.question_id
WHERE pca.challenge_id = :challengeId
ORDER BY pca.points DESC, u.name ASC, paq.question_id ASC
SQL;

        /** @var array<PlayerAnswerRow> $rows */
        $rows = $this->database
            ->executeQuery($query, [
                'challengeId' => $challengeId->toString(),
            ])
            ->fetchAllAssociative();

        // Group rows by user
        $playerAnswersGrouped = [];
        foreach ($rows as $row) {
            $playerId = $row['user_id'];
            if (!isset($playerAnswersGrouped[$playerId])) {
                $playerAnswersGrouped[$playerId] = [];
            }
            $playerAnswersGrouped[$playerId][] = $row;
        }

        // Convert to player data objects
        $players = [];
        foreach ($playerAnswersGrouped as $playerAnswers) {
            $players[] = PlayerAnswerData::fromArray($playerAnswers, $userId);
        }

        return new ChallengeAnswersResponse(
            id: $challengeId,
            players: $players,
        );
    }
}

<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Api\PlayerAnswers;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use Doctrine\DBAL\Connection;
use Symfony\Component\Uid\Uuid;

/**
 * @phpstan-import-type PlayerChallengeAnswerRow from PlayerAnswersResponse
 *
 * @implements ProviderInterface<PlayerAnswersResponse>
 */
readonly final class PlayerAnswersProvider implements ProviderInterface
{
    public function __construct(
        private Connection $database,
    ) {}

    /**
     * @param array{id?: Uuid} $uriVariables
     */
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): PlayerAnswersResponse
    {
        assert(isset($uriVariables['id']));
        $playerId = $uriVariables['id'];

        return $this->getPlayerAnswers($playerId);
    }

    private function getPlayerAnswers(Uuid $playerId): PlayerAnswersResponse
    {
        // Get all player answers for evaluated challenges only, ordered by challenge evaluation date DESC
        $query = <<<SQL
SELECT
    c.id AS challenge_id,
    c.name AS challenge_name,
    c.evaluated_at AS challenge_evaluated_at,
    c.gameweek,
    pca.points,
    paq.question_id,
    q.text AS question_text,
    paq.text_answer,
    paq.numeric_answer,
    paq.selected_choice_id,
    paq.selected_choice_ids,
    paq.ordered_choice_ids
FROM player_challenge_answer pca
JOIN challenge c ON c.id = pca.challenge_id
JOIN player_answered_question paq ON paq.challenge_answer_id = pca.id
JOIN question q ON q.id = paq.question_id
WHERE pca.user_id = :playerId
  AND c.evaluated_at IS NOT NULL
ORDER BY c.evaluated_at DESC, paq.question_id ASC
SQL;

        /** @var array<PlayerChallengeAnswerRow> $rows */
        $rows = $this->database
            ->executeQuery($query, [
                'playerId' => $playerId->toString(),
            ])
            ->fetchAllAssociative();

        // Group rows by challenge
        $challengeAnswersGrouped = [];
        foreach ($rows as $row) {
            $challengeId = $row['challenge_id'];
            if (!isset($challengeAnswersGrouped[$challengeId])) {
                $challengeAnswersGrouped[$challengeId] = [];
            }
            $challengeAnswersGrouped[$challengeId][] = $row;
        }

        // Convert to challenge data objects
        $challenges = [];
        foreach ($challengeAnswersGrouped as $challengeAnswers) {
            $challenges[] = PlayerChallengeData::fromArray($challengeAnswers);
        }

        return new PlayerAnswersResponse(
            id: $playerId,
            challenges: $challenges,
        );
    }
}

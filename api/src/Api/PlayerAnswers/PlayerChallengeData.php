<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Api\PlayerAnswers;

use FantasyAcademy\API\Api\Shared\QuestionAnswerWithCorrect;
use Symfony\Component\Uid\Uuid;

/**
 * @phpstan-import-type PlayerChallengeAnswerRow from PlayerAnswersResponse
 */
readonly final class PlayerChallengeData
{
    /**
     * @param array<QuestionAnswerWithCorrect> $questions
     */
    public function __construct(
        public Uuid $challengeId,
        public string $challengeName,
        public int $points,
        public array $questions,
        public null|int $gameweek,
    ) {
    }

    /**
     * @param array<PlayerChallengeAnswerRow> $challengeAnswers
     */
    public static function fromArray(array $challengeAnswers): self
    {
        // All rows for a challenge have the same challenge info
        $firstRow = $challengeAnswers[0];

        $questions = [];
        foreach ($challengeAnswers as $row) {
            $questions[] = QuestionAnswerWithCorrect::fromArray($row);
        }

        return new self(
            challengeId: Uuid::fromString($firstRow['challenge_id']),
            challengeName: $firstRow['challenge_name'],
            points: $firstRow['points'] ?? 0,
            questions: $questions,
            gameweek: $firstRow['gameweek'],
        );
    }
}

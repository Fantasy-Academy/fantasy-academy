<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Api\ChallengeAnswers;

use Symfony\Component\Uid\Uuid;

/**
 * @phpstan-import-type PlayerAnswerRow from ChallengeAnswersResponse
 */
readonly final class PlayerAnswerData
{
    /**
     * @param array<PlayerQuestionAnswer> $questions
     */
    public function __construct(
        public Uuid $userId,
        public string $userName,
        public bool $isMyself,
        public int $points,
        public array $questions,
    ) {
    }

    /**
     * @param array<PlayerAnswerRow> $playerAnswers
     */
    public static function fromArray(array $playerAnswers, null|Uuid $currentUserId): self
    {
        // All rows for a player have the same user info
        $firstRow = $playerAnswers[0];

        $questions = [];
        foreach ($playerAnswers as $row) {
            $questions[] = PlayerQuestionAnswer::fromArray($row);
        }

        return new self(
            userId: Uuid::fromString($firstRow['user_id']),
            userName: $firstRow['user_name'],
            isMyself: $firstRow['user_id'] === $currentUserId?->toString(),
            points: $firstRow['points'],
            questions: $questions,
        );
    }
}

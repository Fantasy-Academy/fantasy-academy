<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Api\ChallengeAnswers;

use FantasyAcademy\API\Doctrine\AnswerDoctrineType;
use FantasyAcademy\API\Value\Answer;
use Symfony\Component\Uid\Uuid;

/**
 * @phpstan-import-type PlayerAnswerRow from ChallengeAnswersResponse
 */
readonly final class PlayerQuestionAnswer
{
    public function __construct(
        public Uuid $questionId,
        public string $questionText,
        public Answer $answer,
    ) {
    }

    /**
     * @param PlayerAnswerRow $row
     */
    public static function fromArray(array $row): self
    {
        $selectedChoiceIds = null;
        $orderedChoiceIds = null;

        if (is_string($row['selected_choice_ids']) && json_validate($row['selected_choice_ids'])) {
            /** @var null|array<string> $decoded */
            $decoded = json_decode($row['selected_choice_ids'], associative: true);
            $selectedChoiceIds = $decoded;
        }

        if (is_string($row['ordered_choice_ids']) && json_validate($row['ordered_choice_ids'])) {
            /** @var null|array<string> $decoded */
            $decoded = json_decode($row['ordered_choice_ids'], associative: true);
            $orderedChoiceIds = $decoded;
        }

        $answerData = [
            'text_answer' => $row['text_answer'],
            'numeric_answer' => $row['numeric_answer'],
            'selected_choice_id' => $row['selected_choice_id'],
            'selected_choice_ids' => $selectedChoiceIds,
            'ordered_choice_ids' => $orderedChoiceIds,
        ];

        return new self(
            questionId: Uuid::fromString($row['question_id']),
            questionText: $row['question_text'],
            answer: AnswerDoctrineType::createAnswerFromArray($answerData),
        );
    }
}

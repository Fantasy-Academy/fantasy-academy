<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Result;

/**
 * @phpstan-type PlayerQuestionAnswerRowArray array{
 *     player_id: string,
 *     challenge_id: string,
 *     question_id: string,
 *     question_name: string,
 *     text_answer: null|string,
 *     numeric_answer: null|float,
 *     selected_choice_text: null|string,
 *     selected_choice_texts: null|string,
 *     ordered_choice_texts: null|string,
 *     challenge_answer_id: string,
 * }
 */
readonly final class PlayerQuestionAnswerRow
{
    public function __construct(
        public string $playerId,
        public string $challengeId,
        public string $questionId,
        public string $questionName,
        public null|string $textAnswer,
        public null|float $numericAnswer,
        public null|string $selectedChoiceText,
        public null|string $selectedChoiceTexts,
        public null|string $orderedChoiceTexts,
        public string $challengeAnswerId,
    ) {
    }

    /**
     * @param PlayerQuestionAnswerRowArray $data
     */
    public static function createFromArray(array $data): self
    {
        return new self(
            playerId: $data['player_id'],
            challengeId: $data['challenge_id'],
            questionId: $data['question_id'],
            questionName: $data['question_name'],
            textAnswer: $data['text_answer'],
            numericAnswer: $data['numeric_answer'],
            selectedChoiceText: $data['selected_choice_text'],
            selectedChoiceTexts: $data['selected_choice_texts'],
            orderedChoiceTexts: $data['ordered_choice_texts'],
            challengeAnswerId: $data['challenge_answer_id'],
        );
    }
}

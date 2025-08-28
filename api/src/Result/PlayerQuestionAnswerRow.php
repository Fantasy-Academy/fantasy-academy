<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Result;

/**
 * @phpstan-type PlayerQuestionAnswerRowArray array{
 *     player_id: null|string,
 *     challenge_id: null|string,
 *     question_id: null|string,
 *     question_name: null|string,
 *     text_answer: null|string,
 *     numeric_answer: null|string,
 *     selected_choice_text: null|string,
 *     selected_choice_texts: null|string,
 *     ordered_choice_texts: null|string,
 *     challenge_answer_id: null|string,
 * }
 */
readonly final class PlayerQuestionAnswerRow
{
    public function __construct(
        public string $playerId,
        public string $challengeId,
        public string $questionId,
        public string $questionName,
        public string $textAnswer,
        public string $numericAnswer,
        public string $selectedChoiceText,
        public string $selectedChoiceTexts,
        public string $orderedChoiceTexts,
        public string $challengeAnswerId,
    ) {
    }

    /**
     * @param PlayerQuestionAnswerRowArray $data
     */
    public static function createFromArray(array $data): self
    {
        return new self(
            playerId: $data['player_id'] ?? '',
            challengeId: $data['challenge_id'] ?? '',
            questionId: $data['question_id'] ?? '',
            questionName: $data['question_name'] ?? '',
            textAnswer: $data['text_answer'] ?? '',
            numericAnswer: $data['numeric_answer'] ?? '',
            selectedChoiceText: $data['selected_choice_text'] ?? '',
            selectedChoiceTexts: $data['selected_choice_texts'] ?? '',
            orderedChoiceTexts: $data['ordered_choice_texts'] ?? '',
            challengeAnswerId: $data['challenge_answer_id'] ?? '',
        );
    }
}

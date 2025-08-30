<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Value;

use DateTimeImmutable;
use Symfony\Component\Uid\Uuid;

/**
 * @phpstan-import-type NumericQuestionConstraintArray from NumericQuestionConstraint
 * @phpstan-import-type ChoiceQuestionConstraintArray from ChoiceQuestionConstraint
 * @phpstan-type QuestionRow array{
 *     id: string,
 *     text: string,
 *     type: string,
 *     image: null|string,
 *     numeric_constraint: null|string,
 *     choice_constraint: null|string,
 *     challenge_id: string,
 *     answered_at: null|string,
 *     text_answer: null|string,
 *     numeric_answer: null|string,
 *     selected_choice_id: null|string,
 *     selected_choice_ids: null|string,
 *     ordered_choice_ids: null|string,
 * }
 */
readonly final class Question
{
    public function __construct(
        public Uuid $id,
        public string $text,
        public QuestionType $type,
        public null|string $image,
        public null|NumericQuestionConstraint $numericConstraint,
        public null|ChoiceQuestionConstraint $choiceConstraint,
        public null|DateTimeImmutable $answeredAt,
        public null|Answer $answer,
        public null|Answer $myAnswer,
        public null|Answer $correctAnswer,
    ) {}

    /**
     * @param QuestionRow $row
     */
    public static function fromArray(array $row): self
    {
        $numericConstraint = null;
        $choiceConstraint = null;

        if (json_validate($row['numeric_constraint'] ?? '')) {
            /** @var NumericQuestionConstraintArray $numericConstraintData */
            $numericConstraintData = json_decode($row['numeric_constraint'], associative: true);
            $numericConstraint = NumericQuestionConstraint::fromArray($numericConstraintData);
        }

        if (json_validate($row['choice_constraint'] ?? '')) {
            /** @var ChoiceQuestionConstraintArray $choiceConstraintData */
            $choiceConstraintData = json_decode($row['choice_constraint'], associative: true);
            $choiceConstraint = ChoiceQuestionConstraint::fromArray($choiceConstraintData);
        }

        return new self(
            id: Uuid::fromString($row['id']),
            text: $row['text'],
            type: QuestionType::from($row['type']),
            image: $row['image'],
            numericConstraint: $numericConstraint,
            choiceConstraint: $choiceConstraint,
            answeredAt: $row['answered_at'] !== null ? new DateTimeImmutable($row['answered_at']) : null,
            answer: $row['answered_at'] !== null ? Answer::fromArray($row) : null,
            myAnswer: $row['answered_at'] !== null ? Answer::fromArray($row) : null,
            correctAnswer: null,
        );
    }
}

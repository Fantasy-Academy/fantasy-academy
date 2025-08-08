<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Value;

use DateTimeImmutable;
use Symfony\Component\Uid\Uuid;

/**
 * @phpstan-type QuestionRow array{}
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
        public array $answer,
    ) {}

    /**
     * @param QuestionRow $row
     */
    public static function fromRow(array $row): self
    {
        return new self(
            id: Uuid::fromString('0c9c7063-49bf-41b6-85ab-543f7b3641af'),
            text: 'Some other question?',
            type: QuestionType::Numeric,
            image: 'https://placecats.com/800/600',
            numericConstraint: new NumericQuestionConstraint(
                min: 1,
                max: 20,
            ),
            choiceConstraint: null,
        );
    }
}

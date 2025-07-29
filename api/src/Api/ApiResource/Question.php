<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Api\ApiResource;

use Ramsey\Uuid\UuidInterface;

readonly final class Question
{
    public function __construct(
        public UuidInterface $id,
        public string $text,
        public QuestionType $type,
        public null|string $image,
        public null|NumericQuestionConstraint $numericConstraint,
        public null|ChoiceQuestionConstraint $choiceConstraint,
    ) {}
}

<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use FantasyAcademy\API\Doctrine\ChoiceQuestionConstraintDoctrineType;
use FantasyAcademy\API\Doctrine\NumericQuestionConstraintDoctrineType;
use FantasyAcademy\API\Value\ChoiceQuestionConstraint;
use FantasyAcademy\API\Value\NumericQuestionConstraint;
use FantasyAcademy\API\Value\QuestionType;
use JetBrains\PhpStorm\Immutable;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

#[Entity]
class Question
{
    public function __construct(
        #[Id]
        #[Immutable]
        #[Column(type: UuidType::NAME, unique: true)]
        public Uuid $id,

        #[ManyToOne]
        #[JoinColumn(nullable: false)]
        readonly public Challenge $challenge,

        #[Column(type: Types::TEXT)]
        readonly public string $text,

        #[Column]
        readonly public QuestionType $type,

        #[Column(nullable: true)]
        readonly public null|string $image,

        #[Column(type: NumericQuestionConstraintDoctrineType::NAME, nullable: true)]
        public null|NumericQuestionConstraint $numericConstraint,

        #[Column(type: ChoiceQuestionConstraintDoctrineType::NAME, nullable: true)]
        public null|ChoiceQuestionConstraint $choiceConstraint,
    ) {
    }
}

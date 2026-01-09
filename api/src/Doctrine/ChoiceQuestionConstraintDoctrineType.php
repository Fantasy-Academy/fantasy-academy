<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Doctrine;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\JsonType;
use FantasyAcademy\API\Value\Choice;
use FantasyAcademy\API\Value\ChoiceQuestionConstraint;
use Symfony\Component\Uid\Uuid;

/**
 * @phpstan-type ChoiceArray array{
 *     id: string,
 *     text: string,
 *     description: null|string,
 *     image: null|string,
 * }
 * @phpstan-type ChoiceQuestionConstraintArray array{
 *     choices: array<ChoiceArray>,
 *     min_selections: null|int,
 *     max_selections: null|int,
 * }
 */
final class ChoiceQuestionConstraintDoctrineType extends JsonType
{
    public const string NAME = 'choice_question_constraint';

    /**
     * @param ChoiceArray $data
     */
    public static function createChoiceFromArray(array $data): Choice
    {
        return new Choice(
            id: Uuid::fromString($data['id']),
            text: $data['text'],
            description: $data['description'],
            image: $data['image'],
        );
    }

    /**
     * @return ChoiceArray
     */
    public static function transformChoiceToArray(Choice $value): array
    {
        return [
            'id' => $value->id->toString(),
            'text' => $value->text,
            'description' => $value->description,
            'image' => $value->image,
        ];
    }

    /**
     * @param ChoiceQuestionConstraintArray $data
     */
    public static function createChoiceQuestionConstraintFromArray(array $data): ChoiceQuestionConstraint
    {
        return new ChoiceQuestionConstraint(
            choices: array_map(
                callback: fn (array $choiceData): Choice => self::createChoiceFromArray($choiceData),
                array: $data['choices'],
            ),
            minSelections: $data['min_selections'],
            maxSelections: $data['max_selections'],
        );
    }

    /**
     * @return ChoiceQuestionConstraintArray
     */
    public static function transformChoiceQuestionConstraintToArray(ChoiceQuestionConstraint $value): array
    {
        return [
            'choices' => array_map(
                callback: fn (Choice $choice): array => self::transformChoiceToArray($choice),
                array: $value->choices,
            ),
            'min_selections' => $value->minSelections,
            'max_selections' => $value->maxSelections,
        ];
    }

    /**
     * @throws ConversionException
     */
    public function convertToPHPValue(mixed $value, AbstractPlatform $platform): null|ChoiceQuestionConstraint
    {
        if ($value === null) {
            return null;
        }

        /** @var ChoiceQuestionConstraintArray $jsonData */
        $jsonData = parent::convertToPHPValue($value, $platform);

        return self::createChoiceQuestionConstraintFromArray($jsonData);
    }

    /**
     * @param null|ChoiceQuestionConstraint $value
     * @throws ConversionException
     */
    public function convertToDatabaseValue(mixed $value, AbstractPlatform $platform): ?string
    {
        if ($value === null) {
            return null;
        }

        $data = self::transformChoiceQuestionConstraintToArray($value);

        return parent::convertToDatabaseValue($data, $platform);
    }
}

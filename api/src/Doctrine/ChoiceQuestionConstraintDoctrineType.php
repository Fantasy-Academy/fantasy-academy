<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Doctrine;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\JsonbType;
use FantasyAcademy\API\Value\ChoiceQuestionConstraint;

/**
 * @phpstan-import-type ChoiceQuestionConstraintArray from ChoiceQuestionConstraint
 */
final class ChoiceQuestionConstraintDoctrineType extends JsonbType
{
    public const string NAME = 'choice_question_constraint';

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

        return ChoiceQuestionConstraint::fromArray($jsonData);
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

        $data = $value->toArray();

        return parent::convertToDatabaseValue($data, $platform);
    }
}

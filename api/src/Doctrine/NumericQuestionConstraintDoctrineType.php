<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Doctrine;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\JsonbType;
use FantasyAcademy\API\Value\NumericQuestionConstraint;

/**
 * @phpstan-import-type NumericQuestionConstraintArray from NumericQuestionConstraint
 */
final class NumericQuestionConstraintDoctrineType extends JsonbType
{
    public const string NAME = 'numeric_question_constraint';

    /**
     * @throws ConversionException
     */
    public function convertToPHPValue(mixed $value, AbstractPlatform $platform): null|NumericQuestionConstraint
    {
        if ($value === null) {
            return null;
        }

        /** @var NumericQuestionConstraintArray $jsonData */
        $jsonData = parent::convertToPHPValue($value, $platform);

        return NumericQuestionConstraint::fromArray($jsonData);
    }

    /**
     * @param null|NumericQuestionConstraint $value
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

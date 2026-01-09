<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Doctrine;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\JsonType;
use FantasyAcademy\API\Value\NumericQuestionConstraint;

/**
 * @phpstan-type NumericQuestionConstraintArray array{
 *     min: null|int,
 *     max: null|int,
 * }
 */
final class NumericQuestionConstraintDoctrineType extends JsonType
{
    public const string NAME = 'numeric_question_constraint';

    /**
     * @param NumericQuestionConstraintArray $data
     */
    public static function createNumericQuestionConstraintFromArray(array $data): NumericQuestionConstraint
    {
        return new NumericQuestionConstraint(
            min: $data['min'],
            max: $data['max'],
        );
    }

    /**
     * @return NumericQuestionConstraintArray
     */
    public static function transformNumericQuestionConstraintToArray(NumericQuestionConstraint $value): array
    {
        return [
            'min' => $value->min,
            'max' => $value->max,
        ];
    }

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

        return self::createNumericQuestionConstraintFromArray($jsonData);
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

        $data = self::transformNumericQuestionConstraintToArray($value);

        return parent::convertToDatabaseValue($data, $platform);
    }
}

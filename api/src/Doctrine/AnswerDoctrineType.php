<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Doctrine;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\JsonbType;
use FantasyAcademy\API\Value\Answer;

/**
 * @phpstan-import-type AnswerRow from Answer
 */
final class AnswerDoctrineType extends JsonbType
{
    public const string NAME = 'answer';

    /**
     * @throws ConversionException
     */
    public function convertToPHPValue(mixed $value, AbstractPlatform $platform): null|Answer
    {
        if ($value === null) {
            return null;
        }

        /** @var AnswerRow $jsonData */
        $jsonData = parent::convertToPHPValue($value, $platform);

        return Answer::fromArray($jsonData);
    }

    /**
     * @param null|Answer $value
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

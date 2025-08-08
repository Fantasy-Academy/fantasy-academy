<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Doctrine;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\JsonbType;
use Symfony\Component\Uid\Uuid;

final class UuidArrayDoctrineType extends JsonbType
{
    public const string NAME = 'uuid[]';

    /**
     * @throws ConversionException
     * @return null|array<Uuid>
     */
    public function convertToPHPValue(mixed $value, AbstractPlatform $platform): null|array
    {
        if ($value === null) {
            return null;
        }

        /** @var array<string> $jsonData */
        $jsonData = parent::convertToPHPValue($value, $platform);

        return array_map(
            callback: fn (string $uuid): Uuid => Uuid::fromString($uuid),
            array: $jsonData,
        );
    }

    /**
     * @param null|array<Uuid> $value
     * @throws ConversionException
     */
    public function convertToDatabaseValue(mixed $value, AbstractPlatform $platform): ?string
    {
        if ($value === null) {
            return null;
        }

        $data = array_map(
            callback: fn (Uuid $uuid): string => $uuid->toString(),
            array: $value,
        );

        return parent::convertToDatabaseValue($data, $platform);
    }
}

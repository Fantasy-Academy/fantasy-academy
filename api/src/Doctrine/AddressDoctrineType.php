<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Doctrine;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\JsonType;
use FantasyAcademy\API\Value\Address;

final class AddressDoctrineType extends JsonType
{
    public const string NAME = 'address';

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return 'JSONB';
    }

    /**
     * @throws ConversionException
     */
    public function convertToPHPValue(mixed $value, AbstractPlatform $platform): null|Address
    {
        if ($value === null) {
            return null;
        }

        /** @var array{
         *    name: string,
         *    street: string,
         *    city: string,
         *    postalCode: string,
         *    countryCode: string,
         * } $data
         */
        $data = parent::convertToPHPValue($value, $platform);

        return Address::fromArray($data);
    }

    /**
     * @param null|Address $value
     * @throws ConversionException
     */
    public function convertToDatabaseValue(mixed $value, AbstractPlatform $platform): nulL|string
    {
        if ($value === null) {
            return null;
        }

        return parent::convertToDatabaseValue($value->toArray(), $platform);
    }
}

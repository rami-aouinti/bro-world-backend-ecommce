<?php

declare(strict_types=1);

namespace Bro\WorldCoreBundle\Domain\Doctrine\DBAL\Types;

use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\DateTimeType;

/**
 * Overrides the vendor implementation to keep compatibility with Doctrine DBAL 4 signatures.
 */
class UTCDateTimeType extends DateTimeType
{
    private static ?DateTimeZone $utcTimezone = null;

    public function convertToDatabaseValue(mixed $value, AbstractPlatform $platform): mixed
    {
        if ($value instanceof DateTimeInterface) {
            if (!$value instanceof DateTimeImmutable) {
                $value = DateTimeImmutable::createFromInterface($value);
            }

            $value = $value->setTimezone($this->getUtcTimezone());
        }

        return parent::convertToDatabaseValue($value, $platform);
    }

    public function convertToPHPValue(mixed $value, AbstractPlatform $platform): ?DateTime
    {
        $datetime = parent::convertToPHPValue($value, $platform);

        if ($datetime === null) {
            return null;
        }

        if ($datetime instanceof DateTimeImmutable) {
            $datetime = DateTime::createFromImmutable($datetime);
        }

        $datetime->setTimezone($this->getUtcTimezone());

        return $datetime;
    }

    public function getName(): string
    {
        return 'utc_datetime';
    }

    private function getUtcTimezone(): DateTimeZone
    {
        if (self::$utcTimezone instanceof DateTimeZone) {
            return self::$utcTimezone;
        }

        self::$utcTimezone = new DateTimeZone('UTC');

        return self::$utcTimezone;
    }
}

<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\Doctrine\DBAL\Type;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Types\Types;

/**
 * A Doctrine type that stores arbitrary PHP objects using PHP serialization.
 *
 * @internal
 */
final class ObjectType extends Type
{
    public function getName(): string
    {
        return Types::OBJECT;
    }

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return $platform->getClobTypeDeclarationSQL($column);
    }

    public function convertToDatabaseValue(mixed $value, AbstractPlatform $platform): ?string
    {
        if ($value === null) {
            return null;
        }

        try {
            return serialize($value);
        } catch (\Throwable $exception) {
            throw new \RuntimeException('Could not serialize value for the "object" Doctrine type.', 0, $exception);
        }
    }

    public function convertToPHPValue(mixed $value, AbstractPlatform $platform): mixed
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (\is_resource($value)) {
            $value = stream_get_contents($value);
        }

        try {
            return unserialize($value, ['allowed_classes' => true]);
        } catch (\Throwable $exception) {
            throw new \RuntimeException('Could not unserialize value for the "object" Doctrine type.', 0, $exception);
        }
    }

    public function requiresSQLCommentHint(AbstractPlatform $platform): bool
    {
        return true;
    }
}

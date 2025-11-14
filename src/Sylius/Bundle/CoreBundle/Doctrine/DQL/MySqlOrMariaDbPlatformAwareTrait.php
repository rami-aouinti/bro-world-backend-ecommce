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

namespace Sylius\Bundle\CoreBundle\Doctrine\DQL;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Platforms\MySQLPlatform;

trait MySqlOrMariaDbPlatformAwareTrait
{
    private function isMySqlOrMariaDbPlatform(AbstractPlatform $platform): bool
    {
        $platformClasses = [
            MySQLPlatform::class,
            'Doctrine\\DBAL\\Platforms\\MySqlPlatform',
            'Doctrine\\DBAL\\Platforms\\AbstractMySQLPlatform',
            'Doctrine\\DBAL\\Platforms\\MariaDBPlatform',
            'Doctrine\\DBAL\\Platforms\\MariaDb1027Platform',
        ];

        foreach ($platformClasses as $platformClass) {
            if (class_exists($platformClass) && is_a($platform, $platformClass, true)) {
                return true;
            }
        }

        $platformClass = $platform::class;

        return str_contains($platformClass, 'MariaDB') || str_contains($platformClass, 'MariaDb');
    }
}

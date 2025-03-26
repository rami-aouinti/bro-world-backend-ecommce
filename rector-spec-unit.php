<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\PhpSpecToPHPUnit\Set\MigrationSetList;
use Rector\Set\ValueObject\LevelSetList;

return static function (RectorConfig $config): void
{
    $config->paths([
        __DIR__ . '/src/Sylius/Component/Addressing/spec',
    ]);

    $config->sets([
        LevelSetList::UP_TO_PHP_82,
        MigrationSetList::PHPSPEC_TO_PHPUNIT,
    ]);
};

<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\PHPUnit\Set\PHPUnitSetList;
use Rector\Set\ValueObject\LevelSetList;

return RectorConfig::configure()
    ->withSets([
        LevelSetList::UP_TO_PHP_82,
        PHPUnitSetList::PHPUNIT_100,
    ])
;

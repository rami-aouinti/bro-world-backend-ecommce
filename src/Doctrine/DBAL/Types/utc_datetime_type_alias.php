<?php

declare(strict_types=1);

namespace App\Doctrine\DBAL\Types;

use Bro\WorldCoreBundle\Domain\Doctrine\DBAL\Types\UTCDateTimeType as VendorUTCDateTimeType;

if (!class_exists(UTCDateTimeType::class)) {
    return;
}

if (!class_exists(VendorUTCDateTimeType::class, false)) {
    class_alias(UTCDateTimeType::class, VendorUTCDateTimeType::class);
}

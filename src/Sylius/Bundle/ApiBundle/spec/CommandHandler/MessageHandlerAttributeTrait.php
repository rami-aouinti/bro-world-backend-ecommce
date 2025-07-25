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

namespace Tests\Sylius\Bundle\ApiBundle\CommandHandler;

use PhpSpec\ObjectBehavior;
use ReflectionClass;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Webmozart\Assert\Assert;

/** @mixin ObjectBehavior */
trait MessageHandlerAttributeTrait
{
    public function testAMessageHandler(): void
    {
        $messageHandlerAttributes = (new ReflectionClass($this->getWrappedObject()::class))
            ->getAttributes(AsMessageHandler::class);

        Assert::count($messageHandlerAttributes, 1);
    }
}

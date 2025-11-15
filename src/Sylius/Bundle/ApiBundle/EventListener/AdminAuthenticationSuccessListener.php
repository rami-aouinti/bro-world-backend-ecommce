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

namespace Sylius\Bundle\ApiBundle\EventListener;

use Bro\WorldCoreBundle\Infrastructure\ValueObject\SymfonyUser;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;

final class AdminAuthenticationSuccessListener
{
    public function onAuthenticationSuccessResponse(AuthenticationSuccessEvent $event): void
    {
        $user = $event->getUser();

        if (!$user instanceof SymfonyUser) {
            return;
        }

        $event->setData($this->appendUserData($event->getData(), $user));
    }

    /**
     * @param array<string, mixed> $data
     *
     * @return array<string, mixed>
     */
    private function appendUserData(array $data, SymfonyUser $user): array
    {
        $data['user'] = [
            'identifier' => $user->getUserIdentifier(),
            'roles' => $user->getRoles(),
        ];

        return $data;
    }
}

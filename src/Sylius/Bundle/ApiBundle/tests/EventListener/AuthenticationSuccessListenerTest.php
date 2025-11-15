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

namespace Tests\Sylius\Bundle\ApiBundle\EventListener;

use Bro\WorldCoreBundle\Infrastructure\ValueObject\SymfonyUser;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\ApiBundle\EventListener\AuthenticationSuccessListener;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserInterface;

final class AuthenticationSuccessListenerTest extends TestCase
{
    private AuthenticationSuccessListener $authenticationSuccessListener;

    protected function setUp(): void
    {
        parent::setUp();
        $this->authenticationSuccessListener = new AuthenticationSuccessListener();
    }

    public function testAddsSymfonyUserDataToAuthenticationTokenResponse(): void
    {
        /** @var SymfonyUser&MockObject $symfonyUser */
        $symfonyUser = $this->createMock(SymfonyUser::class);
        $event = new AuthenticationSuccessEvent([], $symfonyUser, new Response());

        $symfonyUser->expects(self::once())->method('getUserIdentifier')->willReturn('user-identifier');
        $symfonyUser->expects(self::once())->method('getRoles')->willReturn(['ROLE_USER']);

        $this->authenticationSuccessListener->onAuthenticationSuccessResponse($event);

        self::assertSame([
            'user' => [
                'identifier' => 'user-identifier',
                'roles' => ['ROLE_USER'],
            ],
        ], $event->getData());
    }

    public function testDoesNotAddAnythingWhenUserIsNotSymfonyUser(): void
    {
        /** @var UserInterface&MockObject $user */
        $user = $this->createMock(UserInterface::class);
        $event = new AuthenticationSuccessEvent([], $user, new Response());

        $this->authenticationSuccessListener->onAuthenticationSuccessResponse($event);

        self::assertSame([], $event->getData());
    }
}

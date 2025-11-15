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
use Sylius\Bundle\ApiBundle\EventListener\AdminAuthenticationSuccessListener;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserInterface;

final class AdminAuthenticationSuccessListenerTest extends TestCase
{
    private AdminAuthenticationSuccessListener $adminAuthenticationSuccessListener;

    protected function setUp(): void
    {
        parent::setUp();
        $this->adminAuthenticationSuccessListener = new AdminAuthenticationSuccessListener();
    }

    public function testAddsSymfonyUserDataToAdminAuthenticationTokenResponse(): void
    {
        /** @var SymfonyUser&MockObject $symfonyUser */
        $symfonyUser = $this->createMock(SymfonyUser::class);
        $event = new AuthenticationSuccessEvent([], $symfonyUser, new Response());

        $symfonyUser->expects(self::once())->method('getUserIdentifier')->willReturn('admin-identifier');
        $symfonyUser->expects(self::once())->method('getRoles')->willReturn(['ROLE_ADMIN']);

        $this->adminAuthenticationSuccessListener->onAuthenticationSuccessResponse($event);

        self::assertSame([
            'user' => [
                'identifier' => 'admin-identifier',
                'roles' => ['ROLE_ADMIN'],
            ],
        ], $event->getData());
    }

    public function testDoesNotAddAnythingWhenUserIsNotSymfonyUser(): void
    {
        /** @var UserInterface&MockObject $user */
        $user = $this->createMock(UserInterface::class);
        $event = new AuthenticationSuccessEvent([], $user, new Response());

        $this->adminAuthenticationSuccessListener->onAuthenticationSuccessResponse($event);

        self::assertSame([], $event->getData());
    }
}

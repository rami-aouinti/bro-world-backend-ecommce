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

namespace Sylius\Tests\Api\Utils;

use Sylius\Component\User\Repository\UserRepositoryInterface;
use Symfony\Component\Security\Core\User\UserInterface;

trait UserLoginTrait
{
    protected function logInUser(string $userType, string $email, string $password = 'sylius'): array
    {
        /** @var UserRepositoryInterface $shopUserRepository */
        $shopUserRepository = $this->get(sprintf('sylius.repository.%s_user', $userType));

        /** @var UserInterface|null $user */
        $user = $shopUserRepository->findOneByEmail($email);

        $authorizationHeader = self::$kernel->getContainer()->getParameter('sylius.api.authorization_header');

        return ['HTTP_' . $authorizationHeader => 'Basic ' . base64_encode(sprintf('%s:%s', $email, $password))];
    }
}

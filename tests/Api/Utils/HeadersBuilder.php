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

use Sylius\Component\Core\Model\AdminUserInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;
use Symfony\Component\Security\Core\User\UserInterface as BaseUserInterface;

class HeadersBuilder
{
    /** @var array<string, string> */
    private array $headers = [];

    /**
     * @param UserRepositoryInterface<AdminUserInterface> $adminUserRepository
     * @param UserRepositoryInterface<ShopUserInterface> $shopUserRepository
     */
    public function __construct(
        private UserRepositoryInterface $adminUserRepository,
        private UserRepositoryInterface $shopUserRepository,
        private string $authorizationHeader,
    ) {
    }

    public function withJsonContentType(): self
    {
        $this->headers['CONTENT_TYPE'] = 'application/json';

        return $this;
    }

    public function withJsonLdContentType(): self
    {
        $this->headers['CONTENT_TYPE'] = 'application/ld+json';

        return $this;
    }

    public function withMergePatchJsonContentType(): self
    {
        $this->headers['CONTENT_TYPE'] = 'application/merge-patch+json';

        return $this;
    }

    public function withMultipartFormDataContentType(): self
    {
        $this->headers['CONTENT_TYPE'] = 'multipart/form-data';

        return $this;
    }

    public function withJsonAccept(): self
    {
        $this->headers['HTTP_ACCEPT'] = 'application/json';

        return $this;
    }

    public function withJsonLdAccept(): self
    {
        $this->headers['HTTP_ACCEPT'] = 'application/ld+json';

        return $this;
    }

    public function withShopUserAuthorization(string $email, string $password = 'sylius'): self
    {
        $shopUser = $this->shopUserRepository->findOneByEmail($email);

        if (!$shopUser instanceof BaseUserInterface) {
            throw new \InvalidArgumentException(sprintf('Shop user with email "%s" does not exist.', $email));
        }

        $this->headers['HTTP_' . $this->authorizationHeader] = $this->buildBasicHeader($email, $password);

        return $this;
    }

    public function withAdminUserAuthorization(string $email, string $password = 'sylius'): self
    {
        $adminUser = $this->adminUserRepository->findOneByEmail($email);

        if (!$adminUser instanceof BaseUserInterface) {
            throw new \InvalidArgumentException(sprintf('Admin user with email "%s" does not exist.', $email));
        }

        $this->headers['HTTP_' . $this->authorizationHeader] = $this->buildBasicHeader($email, $password);

        return $this;
    }

    private function buildBasicHeader(string $email, string $password): string
    {
        return 'Basic ' . base64_encode(sprintf('%s:%s', $email, $password));
    }

    /** @return array<string, string> */
    public function build(): array
    {
        return $this->headers;
    }
}

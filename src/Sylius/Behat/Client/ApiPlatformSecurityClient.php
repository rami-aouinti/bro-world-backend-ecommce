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

namespace Sylius\Behat\Client;

use Sylius\Behat\Context\Ui\Admin\Helper\SecurePasswordTrait;
use Sylius\Behat\Service\SharedStorageInterface;
use Symfony\Component\BrowserKit\AbstractBrowser;
use Symfony\Component\HttpFoundation\Response;

final class ApiPlatformSecurityClient implements ApiSecurityClientInterface
{
    use SecurePasswordTrait;

    /** @var array<string, string|object> */
    private array $request = [];

    public function __construct(
        private readonly AbstractBrowser $client,
        private readonly SharedStorageInterface $sharedStorage,
        private readonly string $apiUrlPrefix,
        private readonly string $section,
        private readonly string $authorizationHeader,
    ) {
    }

    public function prepareLoginRequest(): void
    {
        $this->request = [
            'url' => sprintf('%s/%s', $this->apiUrlPrefix, $this->section),
            'method' => 'GET',
        ];
    }

    public function setEmail(string $email): void
    {
        $this->request['email'] = $email;
    }

    public function setPassword(string $password): void
    {
        $this->request['password'] = $this->retrieveSecurePassword($password);
    }

    public function call(): void
    {
        $authorizationHeader = $this->formatAuthorizationHeader();

        $this->client->request(
            $this->request['method'],
            $this->request['url'],
            [],
            [],
            [
                'HTTP_ACCEPT' => 'application/ld+json',
                'CONTENT_TYPE' => 'application/ld+json',
                $authorizationHeader => $this->buildBasicHeader(),
            ],
        );

        $response = $this->client->getResponse();
        if ($response->getStatusCode() !== Response::HTTP_UNAUTHORIZED) {
            $this->sharedStorage->set('token', $this->buildBasicHeader());
        } else {
            $this->sharedStorage->set('token', null);
        }
    }

    public function isLoggedIn(): bool
    {
        return $this->client->getResponse()->getStatusCode() !== Response::HTTP_UNAUTHORIZED;
    }

    public function getErrorMessage(): string
    {
        $content = json_decode($this->client->getResponse()->getContent(), true);

        return is_array($content) && isset($content['message']) ? (string) $content['message'] : 'Invalid credentials.';
    }

    public function logOut(): void
    {
        $this->sharedStorage->set('token', null);

        if ($this->sharedStorage->has('cart_token')) {
            $this->sharedStorage->set('previous_cart_token', $this->sharedStorage->get('cart_token'));
        }
    }

    private function buildBasicHeader(): string
    {
        return 'Basic ' . base64_encode(sprintf('%s:%s', $this->request['email'], $this->request['password']));
    }

    private function formatAuthorizationHeader(): string
    {
        return 'HTTP_' . strtoupper(str_replace('-', '_', $this->authorizationHeader));
    }
}

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

namespace Sylius\Bundle\ApiBundle\Application\Tests;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use PHPUnit\Framework\Attributes\Test;

final class PromotionTest extends ApiTestCase
{
    use SetUpTestsTrait;

    public function setUp(): void
    {
        $this->setFixturesFiles(['tests/Application/config/fixtures/promotions.yaml']);
        $this->setUpTest();
    }

    #[Test]
    public function it_gets_resource_collection_as_a_guest_by_custom_path(): void
    {
        static::createClient()->request('GET', '/api/v2/custom/admin/promotions');

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        $this->assertJsonContains([
            '@context' => '/api/v2/contexts/Promotion',
            '@id' => '/api/v2/custom/admin/promotions',
            '@type' => 'hydra:Collection',
            'hydra:member' => [
                [
                  '@type' => 'Promotion',
                  'name' => 'Sunday promotion',
                ],
            ],
            'hydra:totalItems' => 1,
        ]);
    }

    #[Test]
    public function it_gets_resource_collection_as_an_admin_by_custom_path(): void
    {
        static::createClient()->request(
            'GET',
            '/api/v2/custom/admin/promotions',
            ['auth_bearer' => $this->JWTAdminUserToken],
        );

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        $this->assertJsonContains([
            '@context' => '/api/v2/contexts/Promotion',
            '@id' => '/api/v2/custom/admin/promotions',
            '@type' => 'hydra:Collection',
            'hydra:member' => [
                [
                    '@type' => 'Promotion',
                    'name' => 'Sunday promotion',
                ],
            ],
            'hydra:totalItems' => 1,
        ]);
    }
}

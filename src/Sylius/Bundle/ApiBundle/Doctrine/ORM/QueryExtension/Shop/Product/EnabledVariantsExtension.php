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

namespace Sylius\Bundle\ApiBundle\Doctrine\ORM\QueryExtension\Shop\Product;

use ApiPlatform\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Doctrine\Orm\Extension\QueryItemExtensionInterface;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use Sylius\Bundle\ApiBundle\SectionResolver\ShopApiSection;
use Sylius\Bundle\CoreBundle\SectionResolver\SectionProviderInterface;
use Sylius\Component\Core\Model\ProductInterface;

final readonly class EnabledVariantsExtension implements QueryCollectionExtensionInterface, QueryItemExtensionInterface
{
    public function __construct(private SectionProviderInterface $sectionProvider)
    {
    }

    /**
     * @param array<array-key, mixed> $context
     */
    public function applyToCollection(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        ?Operation $operation = null,
        array $context = [],
    ): void {
        $this->modifyQueryBuilder($queryBuilder, $queryNameGenerator, $resourceClass);
    }

    /**
     * @param array<array-key, mixed> $identifiers
     * @param array<array-key, mixed> $context
     */
    public function applyToItem(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        array $identifiers,
        ?Operation $operation = null,
        array $context = [],
    ): void {
        $this->modifyQueryBuilder($queryBuilder, $queryNameGenerator, $resourceClass);
    }

    private function modifyQueryBuilder(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
    ): void {
        if (!is_a($resourceClass, ProductInterface::class, true)) {
            return;
        }

        if (!$this->sectionProvider->getSection() instanceof ShopApiSection) {
            return;
        }

        $rootAlias = $queryBuilder->getRootAliases()[0];
        $variantAliasName = $queryNameGenerator->generateJoinAlias('variant');
        $enabledParameterName = $queryNameGenerator->generateParameterName('enabled');

        $queryBuilder
            ->addSelect($variantAliasName)
            ->leftJoin(sprintf('%s.variants', $rootAlias), $variantAliasName, Join::WITH, sprintf('%s.enabled = :%s', $variantAliasName, $enabledParameterName))
            ->setParameter($enabledParameterName, true)
        ;
    }
}

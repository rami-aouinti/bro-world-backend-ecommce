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

namespace Sylius\Bundle\CoreBundle\Fixture\Factory;

use Sylius\Bundle\CoreBundle\CatalogPromotion\Checker\InForVariantsScopeVariantChecker;
use Sylius\Component\Promotion\Model\CatalogPromotionScopeInterface;
use Sylius\Resource\Factory\FactoryInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/** @implements ExampleFactoryInterface<CatalogPromotionScopeInterface> */
final class CatalogPromotionScopeExampleFactory extends AbstractExampleFactory implements ExampleFactoryInterface
{
    protected OptionsResolver $optionsResolver;

    /** @param FactoryInterface<CatalogPromotionScopeInterface> $catalogPromotionScopeFactory */
    public function __construct(protected readonly FactoryInterface $catalogPromotionScopeFactory)
    {
        $this->optionsResolver = new OptionsResolver();

        $this->configureOptions($this->optionsResolver);
    }

    public function create(array $options = []): CatalogPromotionScopeInterface
    {
        $options = $this->optionsResolver->resolve($options);

        /** @var CatalogPromotionScopeInterface $catalogPromotionScope */
        $catalogPromotionScope = $this->catalogPromotionScopeFactory->createNew();
        $catalogPromotionScope->setType($options['type']);
        $catalogPromotionScope->setConfiguration($options['configuration']);

        return $catalogPromotionScope;
    }

    protected function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefault('type', InForVariantsScopeVariantChecker::TYPE)
            ->setAllowedTypes('type', 'string')
            ->setDefault('configuration', [])
            ->setAllowedTypes('configuration', 'array')
        ;
    }
}

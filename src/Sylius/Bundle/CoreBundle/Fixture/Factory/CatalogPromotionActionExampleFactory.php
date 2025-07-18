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

use Sylius\Bundle\CoreBundle\CatalogPromotion\Calculator\FixedDiscountPriceCalculator;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Calculator\PercentageDiscountPriceCalculator;
use Sylius\Component\Promotion\Model\CatalogPromotionActionInterface;
use Sylius\Resource\Factory\FactoryInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/** @implements ExampleFactoryInterface<CatalogPromotionActionInterface> */
final class CatalogPromotionActionExampleFactory extends AbstractExampleFactory implements ExampleFactoryInterface
{
    protected OptionsResolver $optionsResolver;

    /** @param FactoryInterface<CatalogPromotionActionInterface> $catalogPromotionActionFactory */
    public function __construct(protected readonly FactoryInterface $catalogPromotionActionFactory)
    {
        $this->optionsResolver = new OptionsResolver();

        $this->configureOptions($this->optionsResolver);
    }

    public function create(array $options = []): CatalogPromotionActionInterface
    {
        $options = $this->optionsResolver->resolve($options);

        /** @var CatalogPromotionActionInterface $catalogPromotionAction */
        $catalogPromotionAction = $this->catalogPromotionActionFactory->createNew();
        $catalogPromotionAction->setType($options['type']);
        $catalogPromotionAction->setConfiguration($options['configuration']);

        return $catalogPromotionAction;
    }

    protected function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefault('type', PercentageDiscountPriceCalculator::TYPE)
            ->setAllowedTypes('type', 'string')
            ->setDefault('configuration', [])
            ->setAllowedTypes('configuration', 'array')
            ->setNormalizer('configuration', function (Options $options, array $configuration): array {
                if ($options['type'] !== FixedDiscountPriceCalculator::TYPE) {
                    return $configuration;
                }

                foreach ($configuration as $channelCode => $channelConfiguration) {
                    if (isset($channelConfiguration['amount'])) {
                        $configuration[$channelCode]['amount'] = (int) ($channelConfiguration['amount'] * 100);
                    }
                }

                return $configuration;
            })
        ;
    }
}

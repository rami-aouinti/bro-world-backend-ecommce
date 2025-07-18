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

use Faker\Factory;
use Faker\Generator;
use Sylius\Component\Core\Factory\PromotionRuleFactoryInterface;
use Sylius\Component\Core\Promotion\Checker\Rule\CartQuantityRuleChecker;
use Sylius\Component\Promotion\Model\PromotionRuleInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/** @implements ExampleFactoryInterface<PromotionRuleInterface> */
class PromotionRuleExampleFactory extends AbstractExampleFactory implements ExampleFactoryInterface
{
    protected Generator $faker;

    protected OptionsResolver $optionsResolver;

    /** @param PromotionRuleFactoryInterface<PromotionRuleInterface> $promotionRuleFactory */
    public function __construct(
        protected readonly PromotionRuleFactoryInterface $promotionRuleFactory,
    ) {
        $this->faker = Factory::create();
        $this->optionsResolver = new OptionsResolver();

        $this->configureOptions($this->optionsResolver);
    }

    public function create(array $options = []): PromotionRuleInterface
    {
        $options = $this->optionsResolver->resolve($options);

        /** @var PromotionRuleInterface $promotionRule */
        $promotionRule = $this->promotionRuleFactory->createNew();
        $promotionRule->setType($options['type']);
        $promotionRule->setConfiguration($options['configuration']);

        return $promotionRule;
    }

    protected function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefault('type', CartQuantityRuleChecker::TYPE)
            ->setAllowedTypes('type', 'string')
            ->setDefault('configuration', [
                'count' => $this->faker->randomNumber(1),
            ])
            ->setNormalizer('configuration', function (Options $options, array $configuration): array {
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

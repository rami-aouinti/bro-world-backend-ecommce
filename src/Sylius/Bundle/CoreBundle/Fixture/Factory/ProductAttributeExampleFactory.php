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
use Sylius\Component\Attribute\Factory\AttributeFactoryInterface;
use Sylius\Component\Core\Formatter\StringInflector;
use Sylius\Component\Locale\Model\LocaleInterface;
use Sylius\Component\Product\Model\ProductAttributeInterface;
use Sylius\Resource\Doctrine\Persistence\RepositoryInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/** @implements ExampleFactoryInterface<ProductAttributeInterface> */
class ProductAttributeExampleFactory extends AbstractExampleFactory implements ExampleFactoryInterface
{
    protected Generator $faker;

    protected OptionsResolver $optionsResolver;

    /**
     * @param AttributeFactoryInterface<ProductAttributeInterface> $productAttributeFactory
     * @param RepositoryInterface<LocaleInterface> $localeRepository
     * @param array<string, string> $attributeTypes
     */
    public function __construct(
        protected readonly AttributeFactoryInterface $productAttributeFactory,
        protected readonly RepositoryInterface $localeRepository,
        protected readonly array $attributeTypes,
    ) {
        $this->faker = Factory::create();
        $this->optionsResolver = new OptionsResolver();

        $this->configureOptions($this->optionsResolver);
    }

    public function create(array $options = []): ProductAttributeInterface
    {
        $options = $this->optionsResolver->resolve($options);

        /** @var ProductAttributeInterface $productAttribute */
        $productAttribute = $this->productAttributeFactory->createTyped($options['type']);
        $productAttribute->setCode($options['code']);
        $productAttribute->setTranslatable($options['translatable']);

        foreach ($this->getLocales() as $localeCode) {
            $productAttribute->setCurrentLocale($localeCode);
            $productAttribute->setFallbackLocale($localeCode);

            $productAttribute->setName($options['name']);
        }

        $productAttribute->setConfiguration($options['configuration']);

        return $productAttribute;
    }

    protected function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefault('name', function (): string {
                /** @var string $words */
                $words = $this->faker->words(3, true);

                return $words;
            })
            ->setDefault('translatable', true)
            ->setDefault('code', fn (Options $options): string => StringInflector::nameToCode($options['name']))
            ->setDefault('type', fn (Options $options): string => $this->faker->randomElement(array_keys($this->attributeTypes)))
            ->setDefault('configuration', fn (Options $options): array => [])
            ->setAllowedValues('type', array_keys($this->attributeTypes))
        ;
    }

    /** @return iterable<string> */
    private function getLocales(): iterable
    {
        /** @var LocaleInterface[] $locales */
        $locales = $this->localeRepository->findAll();
        foreach ($locales as $locale) {
            yield $locale->getCode();
        }
    }
}

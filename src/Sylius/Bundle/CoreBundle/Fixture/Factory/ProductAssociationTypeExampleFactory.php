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
use Sylius\Component\Core\Formatter\StringInflector;
use Sylius\Component\Locale\Model\LocaleInterface;
use Sylius\Component\Product\Model\ProductAssociationTypeInterface;
use Sylius\Resource\Doctrine\Persistence\RepositoryInterface;
use Sylius\Resource\Factory\FactoryInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/** @implements ExampleFactoryInterface<ProductAssociationTypeInterface> */
class ProductAssociationTypeExampleFactory extends AbstractExampleFactory implements ExampleFactoryInterface
{
    protected Generator $faker;

    protected OptionsResolver $optionsResolver;

    /**
     * @param FactoryInterface<ProductAssociationTypeInterface> $productAssociationTypeFactory
     * @param RepositoryInterface<LocaleInterface> $localeRepository
     */
    public function __construct(
        protected readonly FactoryInterface $productAssociationTypeFactory,
        protected readonly RepositoryInterface $localeRepository,
    ) {
        $this->faker = Factory::create();
        $this->optionsResolver = new OptionsResolver();

        $this->configureOptions($this->optionsResolver);
    }

    public function create(array $options = []): ProductAssociationTypeInterface
    {
        $options = $this->optionsResolver->resolve($options);

        /** @var ProductAssociationTypeInterface $productAssociationType */
        $productAssociationType = $this->productAssociationTypeFactory->createNew();
        $productAssociationType->setCode($options['code']);

        foreach ($this->getLocales() as $localeCode) {
            $productAssociationType->setCurrentLocale($localeCode);
            $productAssociationType->setFallbackLocale($localeCode);

            $productAssociationType->setName($options['name']);
        }

        return $productAssociationType;
    }

    protected function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefault('name', function (): string {
                /** @var string $words */
                $words = $this->faker->words(3, true);

                return $words;
            })
            ->setDefault('code', fn (Options $options): string => StringInflector::nameToCode($options['name']))
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

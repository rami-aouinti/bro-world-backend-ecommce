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
use Sylius\Bundle\CoreBundle\Fixture\OptionsResolver\LazyOption;
use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Formatter\StringInflector;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ShippingMethodInterface;
use Sylius\Component\Locale\Model\LocaleInterface;
use Sylius\Component\Shipping\Calculator\DefaultCalculators;
use Sylius\Component\Shipping\Model\ShippingCategoryInterface;
use Sylius\Component\Taxation\Model\TaxCategoryInterface;
use Sylius\Resource\Doctrine\Persistence\RepositoryInterface;
use Sylius\Resource\Factory\FactoryInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/** @implements ExampleFactoryInterface<ShippingMethodInterface> */
class ShippingMethodExampleFactory extends AbstractExampleFactory implements ExampleFactoryInterface
{
    protected Generator $faker;

    protected OptionsResolver $optionsResolver;

    /**
     * @param FactoryInterface<ShippingMethodInterface> $shippingMethodFactory
     * @param RepositoryInterface<ZoneInterface> $zoneRepository
     * @param RepositoryInterface<ShippingCategoryInterface> $shippingCategoryRepository
     * @param RepositoryInterface<LocaleInterface> $localeRepository
     * @param RepositoryInterface<TaxCategoryInterface> $taxCategoryRepository
     */
    public function __construct(
        protected readonly FactoryInterface $shippingMethodFactory,
        protected readonly RepositoryInterface $zoneRepository,
        protected readonly RepositoryInterface $shippingCategoryRepository,
        protected readonly RepositoryInterface $localeRepository,
        protected readonly ChannelRepositoryInterface $channelRepository,
        protected readonly RepositoryInterface $taxCategoryRepository,
    ) {
        $this->faker = Factory::create();
        $this->optionsResolver = new OptionsResolver();

        $this->configureOptions($this->optionsResolver);
    }

    public function create(array $options = []): ShippingMethodInterface
    {
        $options = $this->optionsResolver->resolve($options);

        /** @var ShippingMethodInterface $shippingMethod */
        $shippingMethod = $this->shippingMethodFactory->createNew();
        $shippingMethod->setCode($options['code']);
        $shippingMethod->setEnabled($options['enabled']);
        $shippingMethod->setZone($options['zone']);
        $shippingMethod->setCalculator($options['calculator']['type']);
        $shippingMethod->setConfiguration($options['calculator']['configuration']);
        $shippingMethod->setArchivedAt($options['archived_at']);

        if (array_key_exists('category', $options)) {
            $shippingMethod->setCategory($options['category']);
        }

        if (array_key_exists('tax_category', $options) && ($options['tax_category'] instanceof TaxCategoryInterface)) {
            $shippingMethod->setTaxCategory($options['tax_category']);
        }

        foreach ($this->getLocales() as $localeCode) {
            $shippingMethod->setCurrentLocale($localeCode);
            $shippingMethod->setFallbackLocale($localeCode);

            $shippingMethod->setName($options['name']);
            $shippingMethod->setDescription($options['description']);
        }

        foreach ($options['channels'] as $channel) {
            $shippingMethod->addChannel($channel);
        }

        return $shippingMethod;
    }

    protected function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefault('code', fn (Options $options): string => StringInflector::nameToCode($options['name']))
            ->setDefault('name', function (): string {
                /** @var string $words */
                $words = $this->faker->words(3, true);

                return $words;
            })
            ->setDefault('description', fn (Options $options): string => $this->faker->sentence())
            ->setDefault('enabled', fn (Options $options): bool => $this->faker->boolean(90))
            ->setAllowedTypes('enabled', 'bool')
            ->setDefault('zone', LazyOption::randomOne($this->zoneRepository))
            ->setAllowedTypes('zone', ['null', 'string', ZoneInterface::class])
            ->setNormalizer('zone', LazyOption::getOneBy($this->zoneRepository, 'code'))
            ->setDefined('tax_category')
            ->setAllowedTypes('tax_category', ['null', 'string', TaxCategoryInterface::class])
            ->setDefined('category')
            ->setAllowedTypes('category', ['null', 'string', ShippingCategoryInterface::class])
            ->setNormalizer('category', LazyOption::findOneBy($this->shippingCategoryRepository, 'code'))
            ->setDefault('calculator', function (Options $options): array {
                $configuration = [];
                /** @var ChannelInterface $channel */
                foreach ($options['channels'] as $channel) {
                    $configuration[$channel->getCode()] = ['amount' => $this->faker->numberBetween(100, 1000)];
                }

                return [
                    'type' => DefaultCalculators::FLAT_RATE,
                    'configuration' => $configuration,
                ];
            })
            ->setDefault('channels', LazyOption::all($this->channelRepository))
            ->setAllowedTypes('channels', 'array')
            ->setNormalizer('channels', LazyOption::findBy($this->channelRepository, 'code'))
            ->setDefault('archived_at', null)
            ->setAllowedTypes('archived_at', ['null', \DateTimeInterface::class])
        ;

        $resolver->setNormalizer('tax_category', LazyOption::findOneBy($this->taxCategoryRepository, 'code'));
    }

    private function getLocales(): iterable
    {
        /** @var LocaleInterface[] $locales */
        $locales = $this->localeRepository->findAll();
        foreach ($locales as $locale) {
            yield $locale->getCode();
        }
    }
}

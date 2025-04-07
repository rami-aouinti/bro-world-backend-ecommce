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

namespace Sylius\Behat\Context\Setup\Checkout;

use Behat\Behat\Context\Context;
use Behat\Step\Given;
use Sylius\Behat\Service\Factory\AddressFactoryInterface;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Bundle\ApiBundle\Command\Checkout\UpdateCart;
use Sylius\Component\Addressing\Converter\CountryNameConverterInterface;
use Symfony\Component\Messenger\MessageBusInterface;

final readonly class AddressContext implements Context
{
    public function __construct(
        private SharedStorageInterface $sharedStorage,
        private MessageBusInterface $commandBus,
        private AddressFactoryInterface $addressFactory,
        private CountryNameConverterInterface $countryNameConverter,
    ) {
    }

    #[Given('I addressed the cart')]
    #[Given('I addressed the cart to :countryName')]
    #[Given('the customer addressed the cart')]
    public function iAddressedTheCart(?string $countryName = null): void
    {
        $cartToken = $this->sharedStorage->get('cart_token');

        $countryCode = $countryName !== null ? $this->countryNameConverter->convertToCode($countryName) : null;

        $countryCode ?
            $address = $this->addressFactory->createDefaultWithCountryCode($countryCode) :
            $address = $this->addressFactory->createDefault();

        $this->commandBus->dispatch(new UpdateCart(
            orderTokenValue: $cartToken,
            billingAddress: $address,
        ));
    }

    #[Given('/^I have specified the billing (address as "([^"]+)", "([^"]+)", "([^"]+)", "([^"]+)" for "([^"]+)")$/')]
    public function iHaveSpecifiedDefaultBillingAddressForName(): void
    {
        $cartToken = $this->sharedStorage->get('cart_token');

        $this->commandBus->dispatch(new UpdateCart(
            orderTokenValue: $cartToken,
            billingAddress: $this->addressFactory->createDefaultWithCountryCode('US'),
        ));
    }
}

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

namespace Tests\Sylius\Bundle\PromotionBundle\DependencyInjection\Compiler;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use PHPUnit\Framework\Attributes\Test;
use Sylius\Bundle\PromotionBundle\DependencyInjection\Compiler\CompositePromotionCouponEligibilityCheckerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

final class CompositePromotionCouponEligibilityCheckerPassTest extends AbstractCompilerPassTestCase
{
    #[Test]
    public function it_collects_tagged_promotion_coupon_eligibility_checkers(): void
    {
        $this->setDefinition('sylius.checker.promotion_coupon_eligibility', new Definition());
        $this->setDefinition(
            'sylius.promotion_coupon_eligibility_checker.tagged',
            (new Definition())->addTag('sylius.promotion_coupon_eligibility_checker'),
        );

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithArgument(
            'sylius.checker.promotion_coupon_eligibility',
            0,
            [new Reference('sylius.promotion_coupon_eligibility_checker.tagged')],
        );
    }

    protected function registerCompilerPass(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new CompositePromotionCouponEligibilityCheckerPass());
    }
}

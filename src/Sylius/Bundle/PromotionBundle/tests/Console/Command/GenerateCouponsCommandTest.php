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

namespace Tests\Sylius\Bundle\PromotionBundle\Console\Command;

use InvalidArgumentException;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use Sylius\Component\Promotion\Generator\PromotionCouponGeneratorInstruction;
use Sylius\Component\Promotion\Generator\PromotionCouponGeneratorInterface;
use Sylius\Component\Promotion\Model\PromotionInterface;
use Sylius\Component\Promotion\Repository\PromotionRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;

final class GenerateCouponsCommandTest extends KernelTestCase
{
    private Command $command;

    private CommandTester $commandTester;

    /** @var PromotionRepositoryInterface|MockObject */
    private $promotionRepository;

    /** @var PromotionCouponGeneratorInterface|MockObject */
    private $couponGenerator;

    public function setup(): void
    {
        $kernel = static::createKernel();
        $kernel->boot();
        $application = new Application($kernel);

        $this->promotionRepository = $this->createMock(PromotionRepositoryInterface::class);
        $kernel->getContainer()->set('sylius.repository.promotion', $this->promotionRepository);

        $this->couponGenerator = $this->createMock(PromotionCouponGeneratorInterface::class);
        $kernel->getContainer()->set('sylius.generator.promotion_coupon', $this->couponGenerator);

        $this->command = $application->find('sylius:promotion:generate-coupons');
        $this->commandTester = new CommandTester($this->command);
    }

    #[Test]
    public function it_returns_an_error_if_there_is_no_promotion_for_code(): void
    {
        $this->promotionRepository
            ->method('findOneBy')
            ->with($this->equalTo(['code' => 'UNKNOWN_PROMOTION']))
            ->willReturn(null)
        ;

        $this->commandTester->execute([
            'command' => $this->command->getName(),
            'promotion-code' => 'UNKNOWN_PROMOTION',
            'count' => 10,
        ]);

        // the output of the command in the console
        $output = $this->commandTester->getDisplay();
        $this->assertNotEquals($this->commandTester->getStatusCode(), 0);
        $this->assertStringContainsString('No promotion found with this code', $output);
    }

    #[Test]
    public function it_returns_an_error_if_the_promotion_does_not_allow_coupons(): void
    {
        $promotion = $this->createMock(PromotionInterface::class);
        $promotion->method('isCouponBased')->willReturn(false);

        $this->promotionRepository->method('findOneBy')
            ->with($this->equalTo(['code' => 'INVALID_PROMOTION']))
            ->willReturn($promotion)
        ;

        $this->commandTester->execute([
            'command' => $this->command->getName(),
            'promotion-code' => 'INVALID_PROMOTION',
            'count' => 10,
        ]);

        // the output of the command in the console
        $output = $this->commandTester->getDisplay();
        $this->assertNotEquals($this->commandTester->getStatusCode(), 0);
        $this->assertStringContainsString('This promotion is not coupon based', $output);
    }

    #[Test]
    public function it_handles_generator_exceptions_gracefully(): void
    {
        $promotion = $this->createMock(PromotionInterface::class);
        $promotion->method('isCouponBased')->willReturn(true);

        $this->promotionRepository
            ->method('findOneBy')
            ->with($this->equalTo(['code' => 'VALID_PROMOTION']))
            ->willReturn($promotion)
        ;

        $expectedInstructions = new PromotionCouponGeneratorInstruction(
            amount: 10,
            codeLength: 10,
        );

        $this->couponGenerator
            ->method('generate')
            ->with(
                $this->equalTo($promotion),
                $this->equalTo($expectedInstructions),
            )
            ->willThrowException(new InvalidArgumentException('Could not generate'))
        ;

        $this->commandTester->execute([
            'command' => $this->command->getName(),
            'promotion-code' => 'VALID_PROMOTION',
            'count' => 10,
        ]);

        // the output of the command in the console
        $output = $this->commandTester->getDisplay();
        $this->assertEquals($this->commandTester->getStatusCode(), 1);
        $this->assertStringContainsString('Could not generate', $output);
    }

    #[Test]
    public function it_generates_coupons_with_default_length(): void
    {
        $promotion = $this->createMock(PromotionInterface::class);
        $promotion->method('isCouponBased')->willReturn(true);

        $this->promotionRepository
            ->method('findOneBy')
            ->with($this->equalTo(['code' => 'VALID_PROMOTION']))
            ->willReturn($promotion)
        ;

        $expectedInstructions = new PromotionCouponGeneratorInstruction(
            amount: 5,
            codeLength: 10,
        );

        $this->couponGenerator
            ->expects($this->once())
            ->method('generate')
            ->with(
                $this->equalTo($promotion),
                $this->equalTo($expectedInstructions),
            )
        ;

        $this->commandTester->execute([
            'command' => $this->command->getName(),
            'promotion-code' => 'VALID_PROMOTION',
            'count' => 5,
        ]);

        // the output of the command in the console
        $output = $this->commandTester->getDisplay();
        $this->assertEquals($this->commandTester->getStatusCode(), 0);
        $this->assertStringContainsString('Coupons have been generated', $output);
    }

    #[Test]
    public function it_generates_coupons_with_customized_length(): void
    {
        $promotion = $this->createMock(PromotionInterface::class);
        $promotion->method('isCouponBased')->willReturn(true);

        $this->promotionRepository
            ->method('findOneBy')
            ->with($this->equalTo(['code' => 'VALID_PROMOTION']))
            ->willReturn($promotion)
        ;

        $expectedInstructions = new PromotionCouponGeneratorInstruction(
            amount: 10,
            codeLength: 7,
        );

        $this->couponGenerator
            ->expects($this->once())
            ->method('generate')
            ->with(
                $this->equalTo($promotion),
                $this->equalTo($expectedInstructions),
            )
        ;

        $this->commandTester->execute(
            [
                'command' => $this->command->getName(),
                'promotion-code' => 'VALID_PROMOTION',
                'count' => 10,
                '--length' => 7,
            ],
        );

        // the output of the command in the console
        $output = $this->commandTester->getDisplay();
        $this->assertEquals($this->commandTester->getStatusCode(), 0);
        $this->assertStringContainsString('Coupons have been generated', $output);
    }
}

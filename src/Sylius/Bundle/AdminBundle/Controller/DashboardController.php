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

namespace Sylius\Bundle\AdminBundle\Controller;

use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Webmozart\Assert\Assert;

final readonly class DashboardController
{
    public function __construct(
        private ChannelRepositoryInterface $channelRepository,
        private Environment $templatingEngine
    ) {
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function __invoke(Request $request): Response
    {
        /** @var ChannelInterface|null $channel */
        $channel = $this->findChannelByCodeOrFindFirst($request->query->has('channel') ? (string) $request->query->get('channel') : null);

        return new Response($this->templatingEngine->render('@SyliusAdmin/dashboard/index.html.twig', [
            'channel' => $channel,
        ]));
    }

    private function findChannelByCodeOrFindFirst(?string $channelCode): \Sylius\Component\Channel\Model\ChannelInterface
    {
        if (null !== $channelCode) {
            $channel = $this->channelRepository->findOneByCode($channelCode);
            Assert::nullOrIsInstanceOf($channel, ChannelInterface::class);

            return $channel;
        }

        $channel = $this->channelRepository->findBy([], ['id' => 'ASC'], 1)[0] ?? null;
        Assert::nullOrIsInstanceOf($channel, ChannelInterface::class);

        return $channel;
    }
}

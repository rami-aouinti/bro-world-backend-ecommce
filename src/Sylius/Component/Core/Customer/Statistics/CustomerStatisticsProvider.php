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

namespace Sylius\Component\Core\Customer\Statistics;

use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Resource\Doctrine\Persistence\RepositoryInterface;

final class CustomerStatisticsProvider implements CustomerStatisticsProviderInterface
{
    /**
     * @param RepositoryInterface<ChannelInterface> $channelRepository
     */
    public function __construct(
        private OrderRepositoryInterface $orderRepository,
        private RepositoryInterface $channelRepository,
    ) {
    }

    public function getCustomerStatistics(CustomerInterface $customer): CustomerStatistics
    {
        $orders = $this->orderRepository->findForCustomerStatistics($customer);
        if (empty($orders)) {
            return new CustomerStatistics([]);
        }

        $perChannelCustomerStatistics = [];

        /** @var OrderInterface $order */
        foreach ($orders as $order) {
            $channel = $order->getChannel();
            if (null === $channel) {
                continue;
            }

            $channelKey = $channel->getCode();
            if (null !== $channelKey) {
                $channelKey = 'code_' . $channelKey;
            } else {
                $channelId = method_exists($channel, 'getId') ? $channel->getId() : null;
                $channelKey = null !== $channelId ? 'id_' . (string) $channelId : spl_object_hash($channel);
            }

            if (!isset($perChannelCustomerStatistics[$channelKey])) {
                $perChannelCustomerStatistics[$channelKey] = [
                    'channel' => $channel,
                    'orders_count' => 0,
                    'orders_total' => 0,
                ];
            }

            $perChannelCustomerStatistics[$channelKey]['orders_count'] += 1;
            $perChannelCustomerStatistics[$channelKey]['orders_total'] += $order->getTotal();
        }

        return new CustomerStatistics(array_map(
            static function (array $data): PerChannelCustomerStatistics {
                return new PerChannelCustomerStatistics(
                    $data['orders_count'],
                    $data['orders_total'],
                    $data['channel'],
                );
            },
            array_values($perChannelCustomerStatistics),
        ));
    }
}

<?php

/*
 *  This file is part of SplashSync Project.
 *
 *  Copyright (C) Splash Sync  <www.splashsync.com>
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Splash\SyliusSplashPlugin\Helpers;

use Splash\Models\Objects\Order\Status as OrderStatus;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\OrderShippingStates;

/**
 * Identify Splash Status Based on Order Shipping Statuses
 */
class ShippingStatusIdentifier
{
    public static function toSplash(OrderInterface $order): ?string
    {
        $state = $order->getShippingState();
        switch ($state) {
            case OrderShippingStates::STATE_CANCELLED:
                return OrderStatus::CANCELED;
            case OrderShippingStates::STATE_READY:
                return OrderStatus::PROCESSING;
            case OrderShippingStates::STATE_PARTIALLY_SHIPPED:
            case OrderShippingStates::STATE_SHIPPED:
                return OrderStatus::DELIVERED;
        }

        return null;
    }
}

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
use Sylius\Component\Core\OrderCheckoutStates;
use Sylius\Component\Core\OrderPaymentStates;
use Sylius\Component\Core\OrderShippingStates;

/**
 * Identify Splash Status Based on Order Statuses
 */
class OrderStatusIdentifier
{
    /**
     * Identify Order Global Status
     *
     * @param OrderInterface $order
     *
     * @return null|string
     */
    public static function toSplash(OrderInterface $order): ?string
    {
        //====================================================================//
        // First Level => Order Global State
        $orderState = $order->getState();
        switch ($orderState) {
            case OrderInterface::STATE_CANCELLED:
                return OrderStatus::CANCELED;
            case OrderInterface::STATE_CART:
                return OrderStatus::DRAFT;
            case OrderInterface::STATE_NEW:
            case OrderInterface::STATE_FULFILLED:
                if (!self::isPaid($order)) {
                    return OrderStatus::PAYMENT_DUE;
                }
                if (!self::isShipped($order)) {
                    return OrderStatus::PROCESSING;
                }

                return OrderStatus::DELIVERED;
        }

        return null;
    }

    /**
     * Check if Order is Cancelled
     *
     * @param OrderInterface $order
     *
     * @return bool
     */
    public static function isCanceled(OrderInterface $order) : bool
    {
        return (OrderInterface::STATE_CANCELLED === $order->getState());
    }

    /**
     * Check if Order is Draft
     *
     * @param OrderInterface $order
     *
     * @return bool
     */
    public static function isDraft(OrderInterface $order) : bool
    {
        return (OrderCheckoutStates::STATE_COMPLETED !== $order->getCheckoutState());
    }

    /**
     * Check if Order is Validated
     *
     * @param OrderInterface $order
     *
     * @return bool
     */
    public static function isValidated(OrderInterface $order): bool
    {
        return (OrderInterface::STATE_CANCELLED != $order->getState())
            && (OrderCheckoutStates::STATE_COMPLETED === $order->getCheckoutState())
        ;
    }

    /**
     * Check if Order is Processing
     *
     * @param OrderInterface $order
     *
     * @return bool
     */
    public static function isProcessing(OrderInterface $order): bool
    {
        return (OrderInterface::STATE_NEW == $order->getState())
            && self::isValidated($order)
            && self::isPaid($order)
            && !self::isShipped($order)
        ;
    }

    /**
     * Check if Order is Shipped
     *
     * @param OrderInterface $order
     *
     * @return bool
     */
    public static function isShipped(OrderInterface $order): bool
    {
        return in_array(
            $order->getShippingState(),
            array(OrderShippingStates::STATE_PARTIALLY_SHIPPED, OrderShippingStates::STATE_SHIPPED),
            true
        );
    }

    /**
     * Check if Order is Paid
     *
     * @param OrderInterface $order
     *
     * @return bool
     */
    public static function isPaid(OrderInterface $order): bool
    {
        return  in_array(
            $order->getPaymentState(),
            array(
                OrderPaymentStates::STATE_PAID,
                OrderPaymentStates::STATE_PARTIALLY_REFUNDED,
                OrderPaymentStates::STATE_REFUNDED,
            ),
            true
        );
    }
}

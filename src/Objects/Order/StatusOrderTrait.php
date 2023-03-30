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

namespace Splash\SyliusSplashPlugin\Objects\Order;

use Splash\Models\Objects\Order\Status as OrderStatus;
use Splash\SyliusSplashPlugin\Helpers\OrderStatusIdentifier;

/**
 * Sylius Customer Order Status Field
 */
trait StatusOrderTrait
{
    /**
     * Build Customer Order Status Fields using FieldFactory
     */
    protected function buildOrderStatusFields(): void
    {
        $isLogistic = $this->isLogisticMode();
        //====================================================================//
        // Order Current Status
        $this->fieldsFactory()->create(SPL_T_VARCHAR)
            ->identifier("status")
            ->name("Order Status")
            ->microData(
                "http://schema.org/Order",
                $isLogistic ? "mainStatus" : "orderStatus"
            )
            ->addChoice(OrderStatus::CANCELED, "Cancelled")
            ->addChoice(OrderStatus::DRAFT, "Draft")
            ->addChoice(OrderStatus::PROCESSING, "Processing")
            ->addChoice(OrderStatus::IN_TRANSIT, "Shipment Done")
            ->addChoice(OrderStatus::DELIVERED, "Delivered")
            ->isReadOnly()
        ;
    }

    /**
     * Read requested Field
     *
     * @param string $key       Input List Key
     * @param string $fieldName Field Identifier / Name
     */
    protected function getOrderStatusFields(string $key, string $fieldName): void
    {
        if ("status" != $fieldName) {
            return;
        }

        $this->out[$fieldName] = OrderStatusIdentifier::toSplash($this->object);
        unset($this->in[$key]);
    }
}

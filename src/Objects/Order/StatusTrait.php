<?php

/*
 *  This file is part of SplashSync Project.
 *
 *  Copyright (C) 2015-2019 Splash Sync  <www.splashsync.com>
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Splash\Sylius\Objects\Order;

/**
 * Sylius Customer Order Status Field
 */
trait StatusTrait
{
    /**
     * Build Customer Order Status Fields using FieldFactory
     */
    protected function buildStatusFields()
    {
        //====================================================================//
        // Order Current Status
        $this->fieldsFactory()->create(SPL_T_VARCHAR)
            ->Identifier("status")
            ->Name("Order Status")
            ->MicroData("http://schema.org/Order", "orderStatus")
            ->AddChoice("OrderDraft", "Draft")
            ->AddChoice("OrderInTransit", "Shippment Done")
            ->AddChoice("OrderProcessing", "Processing")
            ->AddChoice("OrderDelivered", "Delivered")
            ->isReadOnly();
    }

    /**
     * Read requested Field
     *
     * @param string $key       Input List Key
     * @param string $fieldName Field Identifier / Name
     */
    protected function getStatusFields($key, $fieldName)
    {
        if ("status" != $fieldName) {
            return;
        }

        $this->out[$fieldName] = $this->getOrderStatus();
        unset($this->in[$key]);
    }

    /**
     * Get Order Status Encode Splash Name
     *
     * @return string
     */
    private function getOrderStatus()
    {
        if ($this->isDraft()) {
            return "OrderDraft";
        }

        if ($this->isValidated() && $this->isShipped() && $this->isPaid()) {
            return "OrderDelivered";
        }

        if ($this->isValidated() && $this->isPaid()) {
            return "OrderInTransit";
        }

        if ($this->isValidated()) {
            return "OrderProcessing";
        }

        return "Unknown";
    }
}

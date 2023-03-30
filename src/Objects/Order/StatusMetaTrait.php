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

use Splash\SyliusSplashPlugin\Helpers\OrderStatusIdentifier;

/**
 * Sylius Order Meta Fields
 */
trait StatusMetaTrait
{
    /**
     * Build Fields using FieldFactory
     */
    public function buildMetaFields(): void
    {
        //====================================================================//
        // Order is Cancelled
        $this->fieldsFactory()->create(SPL_T_BOOL)
            ->identifier("isCanceled")
            ->name("Is Canceled")
            ->microData("http://schema.org/OrderStatus", "OrderCanceled")
            ->group("Meta")
            ->isReadOnly()
        ;
        //====================================================================//
        // Order is Draft
        $this->fieldsFactory()->create(SPL_T_BOOL)
            ->identifier("isDraft")
            ->name("Is Draft")
            ->microData("http://schema.org/OrderStatus", "OrderDraft")
            ->group("Meta")
            ->isReadOnly()
        ;
        //====================================================================//
        // Order is Validated
        $this->fieldsFactory()->create(SPL_T_BOOL)
            ->identifier("isValidated")
            ->name("Is Validated")
            ->description("Checkout Completed")
            ->microData("http://schema.org/OrderStatus", "OrderProcessing")
            ->group("Meta")
            ->isReadOnly()
        ;
        //====================================================================//
        // Order is Processing
        $this->fieldsFactory()->create(SPL_T_BOOL)
            ->identifier("isProcessing")
            ->name("Is Processing")
            ->description("Waiting for Shipment")
            ->microData("http://schema.org/OrderStatus", "OrderProcessing")
            ->group("Meta")
            ->isReadOnly()
        ;
        //====================================================================//
        // Order is Shipped
        $this->fieldsFactory()->create(SPL_T_BOOL)
            ->identifier("isShipped")
            ->name("Is Shipped")
            ->description("Shipping Completed")
            ->microData("http://schema.org/OrderStatus", "OrderDelivered")
            ->group("Meta")
            ->isReadOnly()
        ;
        //====================================================================//
        // Order is Paid
        $this->fieldsFactory()->create(SPL_T_BOOL)
            ->identifier("isPaid")
            ->name("Is Paid")
            ->description("Payment Completed")
            ->microData("http://schema.org/OrderStatus", "OrderPaid")
            ->group("Meta")
            ->isReadOnly()
        ;
    }

    /**
     * Read requested Field
     *
     * @param string $key       Input List Key
     * @param string $fieldName Field Identifier / Name
     */
    public function getMetaFields(string $key, string $fieldName): void
    {
        switch ($fieldName) {
            case 'isCanceled':
                $this->out[$fieldName] = OrderStatusIdentifier::isCanceled($this->object);

                break;
            case 'isDraft':
                $this->out[$fieldName] = OrderStatusIdentifier::isDraft($this->object);

                break;
            case 'isValidated':
                $this->out[$fieldName] = OrderStatusIdentifier::isValidated($this->object);

                break;
            case 'isProcessing':
                $this->out[$fieldName] = OrderStatusIdentifier::isProcessing($this->object);

                break;
            case 'isShipped':
                $this->out[$fieldName] = OrderStatusIdentifier::isShipped($this->object);

                break;
            case 'isPaid':
                $this->out[$fieldName] = OrderStatusIdentifier::isPaid($this->object);

                break;
            default:
                return;
        }
        unset($this->in[$key]);
    }
}

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

use Sylius\Component\Core\OrderCheckoutStates;
use Sylius\Component\Core\OrderPaymentStates;
use Sylius\Component\Core\OrderShippingStates;

/**
 * Sylius Order Meta Fields
 */
trait MetaTrait
{
    /**
     * Build Fields using FieldFactory
     */
    public function buildMetaFields()
    {
        //====================================================================//
        // Order is Draft
        $this->fieldsFactory()->create(SPL_T_BOOL)
            ->Identifier("isDraft")
            ->Name("Is Draft")
            ->MicroData("http://schema.org/OrderStatus", "OrderDraft")
            ->group("Meta")
            ->isReadOnly();

        //====================================================================//
        // Order is Validated
        $this->fieldsFactory()->create(SPL_T_BOOL)
            ->Identifier("isValidated")
            ->Name("Is Completed")
            ->description("Checkout Completed")
            ->MicroData("http://schema.org/OrderStatus", "OrderProcessing")
            ->group("Meta")
            ->isReadOnly();

        //====================================================================//
        // Order is Shipped
        $this->fieldsFactory()->create(SPL_T_BOOL)
            ->Identifier("isShipped")
            ->Name("Is Shipped")
            ->description("Shipping Completed")
            ->MicroData("http://schema.org/OrderStatus", "OrderDelivered")
            ->group("Meta")
            ->isReadOnly();

        //====================================================================//
        // Order is Paid
        $this->fieldsFactory()->create(SPL_T_BOOL)
            ->Identifier("isPaid")
            ->Name("Is Paid")
            ->description("Payment Completed")
            ->MicroData("http://schema.org/OrderStatus", "OrderPaid")
            ->group("Meta")
            ->isReadOnly();
    }

    /**
     * Read requested Field
     *
     * @param string $key       Input List Key
     * @param string $fieldName Field Identifier / Name
     */
    public function getMetaFields($key, $fieldName)
    {
        switch ($fieldName) {
            case 'isDraft':
            case 'isValidated':
            case 'isShipped':
            case 'isPaid':
                $this->out[$fieldName] = $this->{$fieldName}();

                break;
            default:
                return;
        }
        unset($this->in[$key]);
    }
    
    

    /**
     * @return bool
     */
    public function isDraft() : bool
    {
        return (OrderCheckoutStates::STATE_COMPLETED !== $this->object->getCheckoutState());
    }
    
    /**
     * @return bool
     */
    public function isValidated(): bool
    {
        return (OrderCheckoutStates::STATE_COMPLETED === $this->object->getCheckoutState());
    }
    
    /**
     * @return bool
     */
    public function isShipped(): bool
    {
        return in_array(
            $this->object->getShippingState(),
            array(OrderShippingStates::STATE_PARTIALLY_SHIPPED, OrderShippingStates::STATE_SHIPPED),
            true
        );
    }
     
    /**
     * @return bool
     */
    public function isPaid(): bool
    {
        return  in_array(
            $this->object->getPaymentState(),
            array(
                OrderPaymentStates::STATE_PAID,
                OrderPaymentStates::STATE_PARTIALLY_REFUNDED,
                OrderPaymentStates::STATE_REFUNDED
            ),
            true
        );
    }    
    
}

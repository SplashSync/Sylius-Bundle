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

use Exception;
use Splash\Client\Splash;
use Splash\Models\Objects\Order\Status as OrderStatus;
use Splash\SyliusSplashPlugin\Helpers\ShippingStatusIdentifier;
use Sylius\Component\Core\OrderShippingStates;
use Sylius\Component\Core\OrderShippingTransitions;
use Sylius\Component\Shipping\Model\ShipmentInterface;

/**
 * Sylius Customer Order Shipping Status Field
 */
trait StatusShippingTrait
{
    /**
     * Build Customer Order Status Fields using FieldFactory
     */
    protected function buildShippingStatusFields(): void
    {
        $isLogistic = $this->isLogisticMode();
        //====================================================================//
        // Order Shipment Status
        $this->fieldsFactory()->create(SPL_T_VARCHAR)
            ->identifier("shipping_status")
            ->name("Shipping Status")
            ->microData(
                "http://schema.org/Order",
                $isLogistic ? "orderStatus" : "shippingStatus"
            )
            ->addChoice(OrderStatus::CANCELED, "Cancelled")
            ->addChoice(OrderStatus::PROCESSING, "Ready")
            ->addChoice(OrderStatus::DELIVERED, "Shipped")
            ->isReadOnly(!$isLogistic)
        ;
    }

    /**
     * Read requested Field
     *
     * @param string $key       Input List Key
     * @param string $fieldName Field Identifier / Name
     */
    protected function getShippingStatusFields(string $key, string $fieldName): void
    {
        if ("shipping_status" != $fieldName) {
            return;
        }
        $this->out[$fieldName] = ShippingStatusIdentifier::toSplash($this->object);
        unset($this->in[$key]);
    }

    /**
     * Write requested Field
     *
     * @param string      $fieldName Field Identifier / Name
     * @param null|string $fieldData Field Data
     */
    protected function setShippingStatusFields(string $fieldName, ?string $fieldData): void
    {
        if ("shipping_status" != $fieldName) {
            return;
        }
        unset($this->in[$fieldName]);
        //====================================================================//
        // Safety Check
        if (empty($fieldData)) {
            return;
        }
        //====================================================================//
        // Compare
        $currentSplash = ShippingStatusIdentifier::toSplash($this->object);
        if ($fieldData == $currentSplash) {
            return;
        }

        //====================================================================//
        // Update Shipping State
        try {
            $this->updateShippingStatus((string) $currentSplash, $fieldData);
        } catch (\Throwable $ex) {
            Splash::log()->err($ex->getMessage());
        }
    }

    /**
     * Update Order Shipping Status
     *
     * @param string $current Current Splash Shipping Status
     * @param string $new     New Splash Shipping Status
     *
     * @throws Exception
     */
    private function updateShippingStatus(string $current, string $new): void
    {
        //====================================================================//
        // Create State Machine
        $stateMachine = $this->stateMachine->get($this->object, OrderShippingTransitions::GRAPH);
        //====================================================================//
        // Cancelled => Ready [FORCED]
        if (OrderStatus::isCanceled($current) && !OrderStatus::isCanceled($new)) {
            //====================================================================//
            // Force Order State to Ready
            $this->object->setShippingState(OrderShippingStates::STATE_READY);
            //================================s====================================//
            // Force Set First Shipment State to Ready
            if ($shipment = $this->getFirstShipment()) {
                $shipment->setState(ShipmentInterface::STATE_READY);
            }
            $current = ShippingStatusIdentifier::toSplash($this->object);
            $this->needUpdate();
        }
        //====================================================================//
        // Ready => Cancelled
        if (OrderStatus::isCanceled($new)) {
            //====================================================================//
            // Move Order State to Ready by State Machine
            $stateMachine->apply(OrderShippingTransitions::TRANSITION_CANCEL);
            //====================================================================//
            // Force Set First Shipment State to Ready
            if ($shipment = $this->getFirstShipment()) {
                $shipment->setState(ShipmentInterface::STATE_CANCELLED);
            }
            $this->needUpdate();

            return;
        }
        //====================================================================//
        // Ready => Shipped
        if (OrderStatus::isShipped($new) || OrderStatus::isDelivered($new)) {
            $stateMachine->apply(OrderShippingTransitions::TRANSITION_SHIP);
            //====================================================================//
            // Force Set First Shipment State to Ready
            if ($shipment = $this->getFirstShipment()) {
                $shipment->setState(ShipmentInterface::STATE_SHIPPED);
                $shipment->setShippedAt(new \DateTime());
            }
            $this->needUpdate();
        }
    }
}

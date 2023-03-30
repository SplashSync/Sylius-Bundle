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

use Sylius\Component\Core\Model\ShipmentInterface;

/**
 * Access to Order First Shipments List
 */
trait ShipmentTrait
{
    /**
     * Build Fields using FieldFactory
     *
     * @return void
     */
    public function buildShipmentFields(): void
    {
        //====================================================================//
        // Only if Logistic Mode Enabled
        if (!$this->isLogisticMode()) {
            return;
        }
        //====================================================================//
        // First Shipment - Tracking Number
        $this->fieldsFactory()->create(SPL_T_INT)
            ->identifier("tracking_number")
            ->name("Tracking Number")
            ->description("[First Shipment] Tracking Number")
            ->microData("http://schema.org/ParcelDelivery", "trackingNumber")
        ;
        //====================================================================//
        // First Shipment - Carrier Code
        $this->fieldsFactory()->create(SPL_T_INT)
            ->identifier("carrier_code")
            ->name("Code")
            ->description("[Shipment] Code")
            ->microData("http://schema.org/ParcelDelivery", "identifier")
            ->isReadOnly()
        ;
        //====================================================================//
        // First Shipment - Carrier Name
        $this->fieldsFactory()->create(SPL_T_INT)
            ->identifier("carrier_name")
            ->name("Name")
            ->description("[Shipment] Name")
            ->microData("http://schema.org/ParcelDelivery", "name")
            ->isReadOnly()
        ;
    }

    /**
     * Write Given Fields
     *
     * @param string      $fieldName Field Identifier / Name
     * @param null|string $fieldData Field Data
     */
    public function setShipmentFields(string $fieldName, ?string $fieldData): void
    {
        switch ($fieldName) {
            case 'tracking_number':
                $shipment = $this->getFirstShipment();
                if ($fieldData && $shipment && ($fieldData != $shipment->getTracking())) {
                    $shipment->setTracking($fieldData);
                    $this->needUpdate();
                }

                break;
            default:
                return;
        }
        unset($this->in[$fieldName]);
    }

    /**
     * Read requested Field
     *
     * @param string $key       Input List Key
     * @param string $fieldName Field Identifier / Name
     */
    protected function getShipmentFields(string $key, string $fieldName): void
    {
        //====================================================================//
        // READ Fields
        switch ($fieldName) {
            case 'tracking_number':
                $shipment = $this->getFirstShipment();
                $this->out[$fieldName] = $shipment ? $shipment->getTracking() : null;

                break;
            case 'carrier_code':
                $shipment = $this->getFirstShipment();
                $method = $shipment ? $shipment->getMethod() : null;
                $this->out[$fieldName] = $method ? $method->getCode() : null;

                break;
            case 'carrier_name':
                $shipment = $this->getFirstShipment();
                $method = $shipment ? $shipment->getMethod() : null;
                $this->out[$fieldName] = $method ? $method->getName() : null;

                break;
            default:
                return;
        }

        unset($this->in[$key]);
    }

    /**
     * Get Order First Shipment
     *
     * @return null|ShipmentInterface
     */
    protected function getFirstShipment(): ?ShipmentInterface
    {
        $shipment = $this->object->getShipments()->first();
        if ($shipment instanceof ShipmentInterface) {
            return $shipment;
        }

        return null;
    }
}

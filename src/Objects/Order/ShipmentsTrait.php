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
 * Access to Order Shipments List
 */
trait ShipmentsTrait
{
    /**
     * @var string
     */
    private static string $shipmentsList = "shipments";

    /**
     * Build Fields using FieldFactory
     *
     * @return void
     */
    public function buildShipmentsFields(): void
    {
        //====================================================================//
        // Shipment - Item ID
        $this->fieldsFactory()->create(SPL_T_INT)
            ->identifier("id")
            ->inList(self::$shipmentsList)
            ->name("ID")
            ->description("[Shipments] ID")
            ->group("Shipments")
            ->isReadOnly()
        ;
        //====================================================================//
        // Shipment - Code
        $this->fieldsFactory()->create(SPL_T_INT)
            ->identifier("code")
            ->inList(self::$shipmentsList)
            ->name("Code")
            ->description("[Shipments] Code")
            ->group("Shipments")
            ->isReadOnly()
        ;
        //====================================================================//
        // Shipment - Name
        $this->fieldsFactory()->create(SPL_T_INT)
            ->identifier("name")
            ->inList(self::$shipmentsList)
            ->name("Name")
            ->description("[Shipments] Name")
            ->group("Shipments")
            ->isReadOnly()
        ;
        //====================================================================//
        // Shipment - Status
        $this->fieldsFactory()->create(SPL_T_VARCHAR)
            ->identifier("state")
            ->inList(self::$shipmentsList)
            ->name("Status")
            ->description("[Shipments] Status")
            ->group("Shipments")
            ->isReadOnly()
        ;
        //====================================================================//
        // Shipment - Tracking Number
        $this->fieldsFactory()->create(SPL_T_VARCHAR)
            ->identifier("tracking")
            ->inList(self::$shipmentsList)
            ->name("Tracking")
            ->description("[Shipments] Tracking")
            ->group("Shipments")
            ->isReadOnly()
        ;
    }

    /**
     * Read requested Field
     *
     * @param string $key       Input List Key
     * @param string $fieldName Field Identifier / Name
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    protected function getShipmentsFields(string $key, string $fieldName): void
    {
        //====================================================================//
        // Check if List field & Init List Array
        $fieldId = self::lists()->initOutput($this->out, self::$shipmentsList, $fieldName);
        if (!$fieldId) {
            return;
        }
        unset($this->in[$key]);
        //====================================================================//
        // Verify List is Not Empty
        /** @var ShipmentInterface[] $shipments */
        $shipments = $this->object->getShipments();
        if (!$this->object->isShippingRequired() || empty($shipments)) {
            return;
        }
        //====================================================================//
        // Fill List with Data
        foreach ($shipments as $index => $shipment) {
            //====================================================================//
            // READ Fields
            switch ($fieldId) {
                case 'id':
                    $value = $shipment->getId();

                    break;
                case 'code':
                    $method = $shipment->getMethod();
                    $value = $method ? $method->getCode() : null;

                    break;
                case 'name':
                    $method = $shipment->getMethod();
                    $value = $method ? $method->getName() : null;

                    break;
                case 'state':
                    $value = $shipment->getState();

                    break;
                case 'tracking':
                    $value = $shipment->getTracking();

                    break;
                default:
                    return;
            }
            //====================================================================//
            // Insert Data in List
            self::lists()->insert($this->out, self::$shipmentsList, $fieldName, $index, $value);
        }
    }
}

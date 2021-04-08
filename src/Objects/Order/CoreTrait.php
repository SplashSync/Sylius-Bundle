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

use Splash\Client\Splash;
use Sylius\Bundle\CoreBundle\Doctrine\ORM\ShipmentRepository as Shipment;

/**
 * Sylius Order Core Fields
 */
trait CoreTrait
{


    /**
     * @var Shipment
     */
    protected Shipment $shipment;

    /**
     * Build Fields using FieldFactory
     */
    public function buildCoreFields()
    {
        //====================================================================//
        // Customer
        $this->fieldsFactory()->Create((string) self::objects()->encode("ThirdParty", SPL_T_ID))
            ->Identifier("customer")
            ->Name("Customer")
            ->MicroData("http://schema.org/Organization", "ID")
            ->isRequired();

        //====================================================================//
        // Customer Email
        $this->fieldsFactory()->create(SPL_T_EMAIL)
            ->Identifier("email")
            ->Name("Customer Email")
            ->MicroData("http://schema.org/ContactPoint", "email")
            ->isReadOnly();

        //====================================================================//
        // Order Number
        $this->fieldsFactory()->create(SPL_T_VARCHAR)
            ->Identifier("number")
            ->Name("Order Reference")
            ->MicroData("http://schema.org/Order", "orderNumber")
            ->isListed()
            ->isReadOnly();

        //====================================================================//
        // Order Date
        $this->fieldsFactory()->create(SPL_T_DATE)
            ->Identifier("checkoutCompletedAt")
            ->Name("Last Name")
            ->MicroData("http://schema.org/Order", "orderDate")
            ->isListed();
        //====================================================================//
        // Shipping DateTime
        $this->fieldsFactory()->create(SPL_T_VARCHAR)
            ->Identifier("collection_time_text")
            ->Name("ClickNCollect")
            ->isListed();

        $this->fieldsFactory()->create(SPL_T_DATE)
            ->Identifier("collection_time")
            ->Name("Delivry Time")
            ->MicroData("http://schema.org/Order", "orderDate")
            ->isListed();


        //====================================================================//
        // Customer Billing Address
        $this->fieldsFactory()->Create((string) self::objects()->encode("Address", SPL_T_ID))
            ->Identifier("billingAddress")
            ->Name("Billing Address")
            ->MicroData("http://schema.org/Order", "billingAddress")
            ->setPreferRead()
            ->isNotTested();
//            ->isRequired();

        //====================================================================//
        // Customer Shipping Address
        $this->fieldsFactory()->Create((string) self::objects()->encode("Address", SPL_T_ID))
            ->Identifier("shippingAddress")
            ->Name("Shipping Address")
            ->MicroData("http://schema.org/Order", "orderDelivery")
            ->setPreferRead()
            ->isNotTested();
//            ->isRequired();

        //====================================================================//
        // Order Note
        $this->fieldsFactory()->create(SPL_T_TEXT)
            ->Identifier("notes")
            ->Name("Order Note")
            ->MicroData("http://schema.org/Order", "description");
    }

    /**
     * Read requested Field
     *
     * @param string $key       Input List Key
     * @param string $fieldName Field Identifier / Name
     */
    public function getCoreFields($key, $fieldName)
    {
        switch ($fieldName) {
            //====================================================================//
            // Static Id Readings
            case 'customer':
                $this->getGenericObject($fieldName, "ThirdParty");

                break;
            case 'email':
                $customer = $this->object->getCustomer();
                $this->out[$fieldName] = $customer ? $customer->getEmail() : "";

                break;
            case 'billingAddress':
            case 'shippingAddress':
                $this->getGenericObject($fieldName, "Address");

                break;
            //====================================================================//
            // Direct Readings
            case 'number':
            case 'notes':
                $this->getGeneric($fieldName);
                break;
            case 'checkoutCompletedAt':
                $this->getGenericDate($fieldName);
                break;
            case 'collection_time_text':
                $result = $this->shipment->getCollectionTime($this->object->getid());
                if($result != null)
                    $this->out[$fieldName] = $result->format("d-m-Y H:i:s");
                else
                    $this->out[$fieldName] = null;
                break;
            case 'collection_time':
                $result = $this->shipment->getCollectionTime($this->object->getid());
                $dateTime = new \DateTime(null,$result->getTimezone());
                $dateTime->setTimestamp($result->getTimestamp());
                if($result != null)
                    $this->out[$fieldName] = $dateTime->format("Y-m-d");
                else
                    $this->out[$fieldName] = null;
                break;
            default:
                return;
        }
        unset($this->in[$key]);
    }

    /**
     * Write Given Fields
     *
     * @param string $fieldName Field Identifier / Name
     * @param mixed  $fieldData Field Data
     */
    public function setCoreFields($fieldName, $fieldData)
    {
        switch ($fieldName) {
            case 'customer':
                $this->setGenericObject($fieldName, $this->getCustomer($fieldData));

                break;
            case 'billingAddress':
            case 'shippingAddress':
                $this->setGenericObject($fieldName, $this->getAddress($fieldData));

                break;
            case 'number':
            case 'notes':
                $this->setGeneric($fieldName, $fieldData);

                break;
            case 'checkoutCompletedAt':
                $this->setGenericDate($fieldName, $fieldData);

                break;
            case 'collection_time':
                $this->setGenericDateTime($fieldName,$fieldData,"shipment");
                break;
            default:
                return;
        }
        unset($this->in[$fieldName]);
    }
}

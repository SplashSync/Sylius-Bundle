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

/**
 * Sylius Order Core Fields
 */
trait CoreTrait
{
    /**
     * Build Fields using FieldFactory
     */
    public function buildCoreFields(): void
    {
        //====================================================================//
        // Customer
        $this->fieldsFactory()
            ->create((string) self::objects()->encode("ThirdParty", SPL_T_ID))
            ->identifier("customer")
            ->name("Customer")
            ->microData("http://schema.org/Organization", "ID")
            ->isRequired()
        ;
        //====================================================================//
        // Customer Email
        $this->fieldsFactory()->create(SPL_T_EMAIL)
            ->identifier("email")
            ->name("Customer Email")
            ->microData("http://schema.org/ContactPoint", "email")
            ->isReadOnly()
        ;
        //====================================================================//
        // Order Number
        $this->fieldsFactory()->create(SPL_T_VARCHAR)
            ->identifier("number")
            ->name("Order Reference")
            ->microData("http://schema.org/Order", "orderNumber")
            ->isListed()
            ->isReadOnly()
        ;
        //====================================================================//
        // Order Date
        $this->fieldsFactory()->create(SPL_T_DATE)
            ->identifier("checkoutCompletedAt")
            ->name("Last Name")
            ->microData("http://schema.org/Order", "orderDate")
            ->isListed()
        ;
        //====================================================================//
        // Customer Billing Address
        $this->fieldsFactory()
            ->create((string) self::objects()->encode("Address", SPL_T_ID))
            ->identifier("billingAddress")
            ->name("Billing Address")
            ->microData("http://schema.org/Order", "billingAddress")
            ->setPreferRead()
            ->isNotTested()
        ;
        //====================================================================//
        // Customer Shipping Address
        $this->fieldsFactory()
            ->create((string) self::objects()->encode("Address", SPL_T_ID))
            ->identifier("shippingAddress")
            ->name("Shipping Address")
            ->microData("http://schema.org/Order", "orderDelivery")
            ->setPreferRead()
            ->isNotTested()
        ;
        //====================================================================//
        // Order Note
        $this->fieldsFactory()->create(SPL_T_TEXT)
            ->identifier("notes")
            ->name("Order Note")
            ->microData("http://schema.org/Order", "description")
        ;
    }

    /**
     * Read requested Field
     *
     * @param string $key       Input List Key
     * @param string $fieldName Field Identifier / Name
     */
    public function getCoreFields(string $key, string $fieldName): void
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
     *
     * @throws Exception
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function setCoreFields(string $fieldName, $fieldData): void
    {
        switch ($fieldName) {
            case 'customer':
                if ($fieldData && is_scalar($fieldData)) {
                    $this->setGenericObject($fieldName, $this->getCustomer((string) $fieldData));
                }

                break;
            case 'billingAddress':
            case 'shippingAddress':
                if ($fieldData && is_scalar($fieldData)) {
                    $this->setGenericObject($fieldName, $this->getAddress((string) $fieldData));
                }

                break;
            case 'number':
            case 'notes':
                $this->setGeneric($fieldName, $fieldData);

                break;
            case 'checkoutCompletedAt':
                $this->setGenericDate($fieldName, $fieldData);

                break;
            default:
                return;
        }
        unset($this->in[$fieldName]);
    }
}

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

use Sylius\Component\Core\Model\AddressInterface;

/**
 * Access to Order Address Fields
 */
trait DeliveryTrait
{
    /**
     * @var null|AddressInterface
     */
    private ?AddressInterface $shippingAddress;

    /**
     * Build Fields using FieldFactory
     *
     * @return void
     */
    protected function buildDeliveryAddressFields(): void
    {
        $groupName = "Delivery";

        //====================================================================//
        // Company
        $this->fieldsFactory()->create(SPL_T_VARCHAR)
            ->identifier("company")
            ->name("[D] Company")
            ->microData("http://schema.org/Organization", "legalName")
            ->group($groupName)
            ->isReadOnly()
        ;
        //====================================================================//
        // Contact Full Name
        $this->fieldsFactory()->create(SPL_T_VARCHAR)
            ->identifier("fullName")
            ->name("[D] Contact Name")
            ->microData("http://schema.org/PostalAddress", "alternateName")
            ->group($groupName)
            ->isReadOnly()
        ;
        //====================================================================//
        // Address
        $this->fieldsFactory()->create(SPL_T_VARCHAR)
            ->identifier("street")
            ->name("[D] Street Address")
            ->microData("http://schema.org/PostalAddress", "streetAddress")
            ->group($groupName)
            ->isReadOnly()
        ;
        //====================================================================//
        // Address Complement
        $this->fieldsFactory()->create(SPL_T_VARCHAR)
            ->identifier("complementaryStreet")
            ->name("[D] Street Extras")
            ->group($groupName)
            ->microData("http://schema.org/PostalAddress", "postOfficeBoxNumber")
            ->isReadOnly()
        ;
        //====================================================================//
        // Zip Code
        $this->fieldsFactory()->create(SPL_T_VARCHAR)
            ->identifier("postcode")
            ->name("[D] Postal Code")
            ->microData("http://schema.org/PostalAddress", "postalCode")
            ->group($groupName)
            ->isReadOnly()
        ;
        //====================================================================//
        // City Name
        $this->fieldsFactory()->create(SPL_T_VARCHAR)
            ->identifier("city")
            ->name("[D] City")
            ->microData("http://schema.org/PostalAddress", "addressLocality")
            ->group($groupName)
            ->isReadOnly()
        ;
        //====================================================================//
        // State Name
        $this->fieldsFactory()->create(SPL_T_VARCHAR)
            ->identifier("provinceName")
            ->name("[D] State")
            ->group($groupName)
            ->isReadOnly()
        ;
        //====================================================================//
        // Country ISO Code
        $this->fieldsFactory()->create(SPL_T_COUNTRY)
            ->identifier("countrycode")
            ->name("[D] Country")
            ->microData("http://schema.org/PostalAddress", "addressCountry")
            ->group($groupName)
            ->isReadOnly()
        ;
        //====================================================================//
        // Other
        $this->fieldsFactory()->create(SPL_T_VARCHAR)
            ->identifier("shipping_note")
            ->name("[D] Other")
            ->description("Other: Remarks, Relay Point Code, more...")
            ->microData("http://schema.org/PostalAddress", "description")
            ->group($groupName)
            ->isReadOnly()
        ;
    }

    /**
     * Read requested Field
     *
     * @param string $key       Input List Key
     * @param string $fieldName Field Identifier / Name
     *
     * @return void
     */
    protected function getDeliveryComputedFields(string $key, string $fieldName): void
    {
        //====================================================================//
        // Custom Customer Address Fields
        switch ($fieldName) {
            case 'fullName':
                $this->shippingAddress = $this->object->getShippingAddress();
                if ($this->shippingAddress) {
                    $this->out[$fieldName] = sprintf(
                        "%s %s",
                        $this->shippingAddress->getFirstName(),
                        $this->shippingAddress->getLastName()
                    );
                } else {
                    $this->out[$fieldName] = null;
                }

                break;
            case 'complementaryStreet':
                $this->shippingAddress = $this->object->getShippingAddress();
                if (!$this->shippingAddress) {
                    $this->out[$fieldName] = null;

                    break;
                }
                if (method_exists($this->shippingAddress, "getComplementaryStreet")) {
                    $this->out[$fieldName] = $this->shippingAddress->getComplementaryStreet();
                }

                break;
            case 'shipping_note':
                $this->out[$fieldName] = $this->object->getNotes();

                break;
            default:
                return;
        }
        unset($this->in[$key]);
    }
    /**
     * Read requested Field
     *
     * @param string $key       Input List Key
     * @param string $fieldName Field Identifier / Name
     *
     * @return void
     */
    protected function getDeliveryGenericFields(string $key, string $fieldName): void
    {
        //====================================================================//
        // Generic Customer Address Fields
        switch ($fieldName) {
            case 'company':
            case 'street':
            case 'postcode':
            case 'city':
            case 'provinceName':
            case 'countrycode':
                $this->shippingAddress = $this->object->getShippingAddress();
                if ($this->shippingAddress) {
                    $this->getGeneric($fieldName, "shippingAddress");
                } else {
                    $this->out[$fieldName] = null;
                }

                break;
            default:
                return;
        }
        unset($this->in[$key]);
    }
}

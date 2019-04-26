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

namespace Splash\Sylius\Objects\Address;

use Splash\Client\Splash;

/**
 * Sylius Address Core Fields
 */
trait CoreTrait
{
    /**
     * Build Fields using FieldFactory
     */
    public function buildCoreFields()
    {
        //====================================================================//
        // Customer
        $this->fieldsFactory()->create((string) self::objects()->encode("ThirdParty", SPL_T_ID))
            ->Identifier("customer")
            ->Name("Customer")
            ->Description("Customer Link")
            ->MicroData("http://schema.org/Organization", "ID")
            ->isRequired();

        //====================================================================//
        // Firstname
        $this->fieldsFactory()->create(SPL_T_VARCHAR)
            ->Identifier("firstname")
            ->Name("First Name")
            ->MicroData("http://schema.org/Person", "familyName")
            ->isListed()
            ->isRequired();

        //====================================================================//
        // Lastname
        $this->fieldsFactory()->create(SPL_T_VARCHAR)
            ->Identifier("lastname")
            ->Name("Last Name")
            ->MicroData("http://schema.org/Person", "givenName")
            ->isListed()
            ->isRequired();

        //====================================================================//
        // Phone
        $this->fieldsFactory()->create(SPL_T_PHONE)
            ->Identifier("phoneNumber")
            ->Name("Phone")
            ->isLogged()
            ->MicroData("http://schema.org/Person", "telephone")
            ->isListed();

        //====================================================================//
        // Company
        $this->fieldsFactory()->create(SPL_T_VARCHAR)
            ->Identifier("company")
            ->Name("Company Name")
            ->MicroData("http://schema.org/Organization", "legalName");

        //====================================================================//
        // Street
        $this->fieldsFactory()->create(SPL_T_VARCHAR)
            ->Identifier("street")
            ->Name("Street")
            ->MicroData("http://schema.org/PostalAddress", "streetAddress")
            ->isRequired();

        //====================================================================//
        // City Name
        $this->fieldsFactory()->create(SPL_T_VARCHAR)
            ->Identifier("city")
            ->Name("City")
            ->MicroData("http://schema.org/PostalAddress", "addressLocality")
            ->isRequired()
            ->isListed();

        //====================================================================//
        // Zip Code
        $this->fieldsFactory()->create(SPL_T_VARCHAR)
            ->Identifier("postcode")
            ->Name("Zip/Postal Code")
            ->MicroData("http://schema.org/PostalAddress", "postalCode")
            ->AddOption("maxLength", 12)
            ->isRequired();

        //====================================================================//
        // Country ISO Code
        $this->fieldsFactory()->create(SPL_T_COUNTRY)
            ->Identifier("countrycode")
            ->Name("Country ISO Code")
            ->MicroData("http://schema.org/PostalAddress", "addressCountry")
            ->isRequired();

        //====================================================================//
        // State Name
        $this->fieldsFactory()->create(SPL_T_VARCHAR)
            ->Identifier("provinceName")
            ->Name("Province Name")
            ->isReadOnly();

        //====================================================================//
        // State Code
        $this->fieldsFactory()->create(SPL_T_STATE)
            ->Identifier("provinceCode")
            ->Name("Province Code")
            ->MicroData("http://schema.org/PostalAddress", "addressRegion")
            ->isReadOnly();
    }

    /**
     * Read requested Field
     *
     * @param string $key       Input List Key
     * @param string $fieldName Field Identifier / Name
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function getCoreFields($key, $fieldName)
    {
        switch ($fieldName) {
            //====================================================================//
            // Static Id Readings
            case 'customer':
                $this->getGenericObject($fieldName, "ThirdParty");

                break;
            //====================================================================//
            // Direct Readings
            case 'firstname':
            case 'lastname':
            case 'company':
            case 'phoneNumber':
            case 'city':
            case 'street':
            case 'postcode':
            case 'countrycode':
            case 'provinceName':
                $this->getGeneric($fieldName);

                break;
            case 'provinceCode':
                $this->out[$fieldName] = $this->getProvinceCode();

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
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function setCoreFields($fieldName, $fieldData)
    {
        switch ($fieldName) {
            case 'customer':
                $this->setGenericObject(
                    $fieldName,
                    $this->customers->find((int) self::objects()->id($fieldData))
                );

                break;
            case 'firstname':
            case 'lastname':
            case 'company':
            case 'phoneNumber':
            case 'city':
            case 'street':
            case 'postcode':
            case 'countrycode':
                $this->setGeneric($fieldName, $fieldData);

                break;
            default:
                return;
        }
        unset($this->in[$fieldName]);
    }

    /**
     * Format Customer Address Province Code
     *
     * @return string
     */
    private function getProvinceCode()
    {
        if (null === $this->object->getCountryCode()) {
            return (string) $this->object->getProvinceCode();
        }

        return substr(
            (string) $this->object->getProvinceCode(),
            strlen($this->object->getCountryCode()) + 1
        );
    }
}

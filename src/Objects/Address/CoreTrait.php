<?php

/*
 *  Copyright (C) BadPixxel <www.badpixxel.com>
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Splash\SyliusSplashPlugin\Objects\Address;

/**
 * Sylius Address Core Fields
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
            ->description("Customer Link")
            ->microData("http://schema.org/Organization", "ID")
            ->isRequired()
        ;
        //====================================================================//
        // Firstname
        $this->fieldsFactory()->create(SPL_T_VARCHAR)
            ->identifier("firstname")
            ->name("First Name")
            ->microData("http://schema.org/Person", "familyName")
            ->isListed()
            ->isRequired()
        ;
        //====================================================================//
        // Lastname
        $this->fieldsFactory()->create(SPL_T_VARCHAR)
            ->identifier("lastname")
            ->name("Last Name")
            ->microData("http://schema.org/Person", "givenName")
            ->isListed()
            ->isRequired()
        ;
        //====================================================================//
        // Phone
        $this->fieldsFactory()->create(SPL_T_PHONE)
            ->identifier("phoneNumber")
            ->name("Phone")
            ->isLogged()
            ->microData("http://schema.org/Person", "telephone")
            ->isListed()
        ;

        //====================================================================//
        // Company
        $this->fieldsFactory()->create(SPL_T_VARCHAR)
            ->identifier("company")
            ->name("Company Name")
            ->microData("http://schema.org/Organization", "legalName")
        ;
        //====================================================================//
        // Street
        $this->fieldsFactory()->create(SPL_T_VARCHAR)
            ->identifier("street")
            ->name("Street")
            ->microData("http://schema.org/PostalAddress", "streetAddress")
            ->isRequired()
        ;
        //====================================================================//
        // City Name
        $this->fieldsFactory()->create(SPL_T_VARCHAR)
            ->identifier("city")
            ->name("City")
            ->microData("http://schema.org/PostalAddress", "addressLocality")
            ->isRequired()
            ->isListed()
        ;
        //====================================================================//
        // Zip Code
        $this->fieldsFactory()->create(SPL_T_VARCHAR)
            ->identifier("postcode")
            ->name("Zip/Postal Code")
            ->microData("http://schema.org/PostalAddress", "postalCode")
            ->addOption("maxLength", 12)
            ->isRequired()
        ;
        //====================================================================//
        // Country ISO Code
        $this->fieldsFactory()->create(SPL_T_COUNTRY)
            ->identifier("countrycode")
            ->name("Country ISO Code")
            ->microData("http://schema.org/PostalAddress", "addressCountry")
            ->isRequired()
        ;
        //====================================================================//
        // State Name
        $this->fieldsFactory()->create(SPL_T_VARCHAR)
            ->identifier("provinceName")
            ->name("Province Name")
            ->isReadOnly()
        ;
        //====================================================================//
        // State Code
        $this->fieldsFactory()->create(SPL_T_STATE)
            ->identifier("provinceCode")
            ->name("Province Code")
            ->microData("http://schema.org/PostalAddress", "addressRegion")
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
    public function getCoreFields(string $key, string $fieldName): void
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
    public function setCoreFields(string $fieldName, $fieldData): void
    {
        switch ($fieldName) {
            case 'customer':
                if ($fieldData && is_string($fieldData)) {
                    $this->setGenericObject(
                        $fieldName,
                        $this->customers->find((int) self::objects()->id($fieldData))
                    );
                }

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
    private function getProvinceCode(): string
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

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

namespace Splash\Sylius\Objects\ThirdParty;

use Splash\Client\Splash;

/**
 * Sylius Customers Core Fields
 */
trait CoreTrait
{
    /**
     * Build Fields using FieldFactory
     */
    public function buildCoreFields()
    {
        //====================================================================//
        // Company
        $this->fieldsFactory()->create(SPL_T_VARCHAR)
            ->Identifier("user_id")
            ->Name("User Id")
            ->Description("User Id")
            ->MicroData("http://schema.org/Organization", "legalName")
            ->isReadOnly();

        //====================================================================//
        // Firstname
        $this->fieldsFactory()->create(SPL_T_VARCHAR)
            ->Identifier("firstname")
            ->Name("First Name")
            ->MicroData("http://schema.org/Person", "familyName")
            ->isListed()
            ->isLogged();

        //====================================================================//
        // Lastname
        $this->fieldsFactory()->create(SPL_T_VARCHAR)
            ->Identifier("lastname")
            ->Name("Last Name")
            ->MicroData("http://schema.org/Person", "givenName")
            ->isListed()
            ->isLogged();

        //====================================================================//
        // Email
        $this->fieldsFactory()->create(SPL_T_EMAIL)
            ->Identifier("email")
            ->Name("Email")
            ->MicroData("http://schema.org/ContactPoint", "email")
            ->isRequired()
            ->isLogged()
            ->isListed();
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
            case 'user_id':
                $this->out[$fieldName] = "Sylius".$this->object->getId();

                break;
            //====================================================================//
            // Direct Readings
            case 'firstname':
            case 'lastname':
            case 'email':
                $this->getGeneric($fieldName);

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
            case 'firstname':
            case 'lastname':
                $this->setGeneric($fieldName, $fieldData);

                break;
            case 'email':
                $this->setGeneric($fieldName, $fieldData);
                $this->object->setEmailCanonical(strtolower($fieldData));

                break;
            default:
                return;
        }
        unset($this->in[$fieldName]);
    }
}

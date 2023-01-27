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

namespace Splash\SyliusSplashPlugin\Objects\ThirdParty;

/**
 * Sylius Customers Core Fields
 */
trait CoreTrait
{
    /**
     * Build Fields using FieldFactory
     */
    public function buildCoreFields(): void
    {
        //====================================================================//
        // Company
        $this->fieldsFactory()->create(SPL_T_VARCHAR)
            ->identifier("user_id")
            ->name("User Id")
            ->description("User Id")
            ->microData("http://schema.org/Organization", "legalName")
            ->isReadOnly()
        ;
        //====================================================================//
        // Firstname
        $this->fieldsFactory()->create(SPL_T_VARCHAR)
            ->identifier("firstname")
            ->name("First Name")
            ->microData("http://schema.org/Person", "familyName")
            ->isListed()
            ->isLogged()
        ;
        //====================================================================//
        // Lastname
        $this->fieldsFactory()->create(SPL_T_VARCHAR)
            ->identifier("lastname")
            ->name("Last Name")
            ->microData("http://schema.org/Person", "givenName")
            ->isListed()
            ->isLogged()
        ;
        //====================================================================//
        // Email
        $this->fieldsFactory()->create(SPL_T_EMAIL)
            ->identifier("email")
            ->name("Email")
            ->microData("http://schema.org/ContactPoint", "email")
            ->isRequired()
            ->isPrimary()
            ->isLogged()
            ->isListed()
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
            case 'user_id':
                $this->out[$fieldName] = "Sylius".$this->object->getId();

                break;
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
     * @param string      $fieldName Field Identifier / Name
     * @param null|string $fieldData Field Data
     */
    public function setCoreFields(string $fieldName, ?string $fieldData): void
    {
        switch ($fieldName) {
            case 'firstname':
            case 'lastname':
                $this->setGeneric($fieldName, $fieldData);

                break;
            case 'email':
                $this->setGeneric($fieldName, $fieldData);
                $this->object->setEmailCanonical(strtolower((string) $fieldData));

                break;
            default:
                return;
        }
        unset($this->in[$fieldName]);
    }
}

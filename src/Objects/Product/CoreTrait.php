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

namespace Splash\Sylius\Objects\Product;

/**
 * Sylius Product Core Fields
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
            ->Identifier("code")
            ->Name("Reference")
            ->Description("Product reference")
            ->MicroData("http://schema.org/Product", "model")
            ->isRequired()->isListed()->isLogged();
                

        //====================================================================//
        // Enable Flag
        $this->fieldsFactory()->create(SPL_T_BOOL)
            ->Identifier("enabled")
            ->Name("Active")
            ->MicroData("http://schema.org/Product", "offered")
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
            // Variant Readings
            case 'code':
                $this->getGeneric($fieldName);

                break;
            //====================================================================//
            // Product Readings
            case 'enabled':
                $this->getGenericBool($fieldName, "product");

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
            //====================================================================//
            // Variant Writting
            case 'code':
                if (!$this->product->getCode()) {
                    $this->product->setCode($fieldData);
                }                
                $this->setGeneric($fieldName, $fieldData);

                break;
            //====================================================================//
            // Product Writting
            case 'enabled':
                $this->setGenericBool($fieldName, $fieldData, "product");

                break;
            default:
                return;
        }
        unset($this->in[$fieldName]);
    }
}

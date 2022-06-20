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

namespace Splash\SyliusSplashPlugin\Objects\Product;

/**
 * Sylius Product Core Fields
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
            ->identifier("code")
            ->name("Reference")
            ->description("Product reference")
            ->microData("http://schema.org/Product", "model")
            ->isRequired()
            ->isListed()
            ->isLogged()
        ;
        //====================================================================//
        // Enable Flag
        $this->fieldsFactory()->create(SPL_T_BOOL)
            ->identifier("enabled")
            ->name("Active")
            ->microData("http://schema.org/Product", "offered")
            ->isListed()
        ;
        //====================================================================//
        // Product Variation Parent Link
        $this->fieldsFactory()->create(SPL_T_VARCHAR)
            ->identifier("parent_id")
            ->name("Parent")
            ->microData("http://schema.org/Product", "isVariationOf")
            ->isReadOnly()
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
            // Variant Readings
            case 'code':
                $this->getGeneric($fieldName);

                break;
            //====================================================================//
            // Product Readings
            case 'enabled':
                $this->getGenericBool($fieldName, "product");

                break;
            case 'parent_id':
                $this->out[$fieldName] = (string) $this->product->getId();

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
     * @param null|scalar $fieldData Field Data
     */
    public function setCoreFields(string $fieldName, $fieldData): void
    {
        switch ($fieldName) {
            //====================================================================//
            // Variant Writing
            case 'code':
                if (!$this->product->getCode()) {
                    $this->product->setCode((string) $fieldData);
                }
                $this->setGeneric($fieldName, $fieldData);

                break;
            //====================================================================//
            // Product Writing
            case 'enabled':
                $this->setGenericBool($fieldName, $fieldData, "product");

                break;
            default:
                return;
        }
        unset($this->in[$fieldName]);
    }
}

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
 * Sylius Product Shipping Fields
 */
trait ShippingTrait
{
    /**
     * Build Fields using FieldFactory
     */
    public function buildShippingFields()
    {
        $groupName = "Shipping";
        
        //====================================================================//
        // Weight
        $this->fieldsFactory()->create(SPL_T_DOUBLE)
            ->Identifier("weight")
            ->Name("Weight")
            ->Group($groupName)
            ->MicroData("http://schema.org/Product", "weight");
        //====================================================================//
        // Height
        $this->fieldsFactory()->create(SPL_T_DOUBLE)
            ->Identifier("height")
            ->Name("Height")
            ->Group($groupName)
            ->MicroData("http://schema.org/Product", "height");
        //====================================================================//
        // Depth
        $this->fieldsFactory()->create(SPL_T_DOUBLE)
            ->Identifier("depth")
            ->Name("Depth")
            ->Group($groupName)
            ->MicroData("http://schema.org/Product", "depth");
        //====================================================================//
        // Width
        $this->fieldsFactory()->create(SPL_T_DOUBLE)
            ->Identifier("width")
            ->Name("Width")
            ->Group($groupName)
            ->MicroData("http://schema.org/Product", "width");
    }

    /**
     * Read requested Field
     *
     * @param string $key       Input List Key
     * @param string $fieldName Field Identifier / Name
     */
    public function getShippingFields($key, $fieldName)
    {
        switch ($fieldName) {
            //====================================================================//
            // Variant Readings
            case 'weight':
            case 'height':
            case 'depth':
            case 'width':
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
    public function setShippingFields($fieldName, $fieldData)
    {
        switch ($fieldName) {
            //====================================================================//
            // Variant Readings
            case 'weight':
            case 'height':
            case 'depth':
            case 'width':
                $this->setGeneric($fieldName, $fieldData);

                break;
            default:
                return;
        }
        unset($this->in[$fieldName]);
    }
}

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

namespace Splash\SyliusSplashPlugin\Objects\Product;

/**
 * Sylius Product Shipping Fields
 */
trait ShippingTrait
{
    /**
     * Build Fields using FieldFactory
     */
    public function buildShippingFields(): void
    {
        $groupName = "Shipping";

        //====================================================================//
        // Weight
        $this->fieldsFactory()->create(SPL_T_DOUBLE)
            ->identifier("weight")
            ->name("Weight")
            ->group($groupName)
            ->microData("http://schema.org/Product", "weight")
        ;
        //====================================================================//
        // Height
        $this->fieldsFactory()->create(SPL_T_DOUBLE)
            ->identifier("height")
            ->name("Height")
            ->group($groupName)
            ->microData("http://schema.org/Product", "height")
        ;
        //====================================================================//
        // Depth
        $this->fieldsFactory()->create(SPL_T_DOUBLE)
            ->identifier("depth")
            ->name("Depth")
            ->group($groupName)
            ->microData("http://schema.org/Product", "depth")
        ;
        //====================================================================//
        // Width
        $this->fieldsFactory()->create(SPL_T_DOUBLE)
            ->identifier("width")
            ->name("Width")
            ->group($groupName)
            ->microData("http://schema.org/Product", "width")
        ;
    }

    /**
     * Read requested Field
     *
     * @param string $key       Input List Key
     * @param string $fieldName Field Identifier / Name
     */
    public function getShippingFields(string $key, string $fieldName): void
    {
        switch ($fieldName) {
            //====================================================================//
            // Variant Readings
            case 'weight':
                $this->out[$fieldName] = $this->unitsManager->normalizeWeight($this->object->getWeight());

                break;
            case 'height':
                $this->out[$fieldName] = $this->unitsManager->normalizeLength($this->object->getHeight());

                break;
            case 'depth':
                $this->out[$fieldName] = $this->unitsManager->normalizeLength($this->object->getDepth());

                break;
            case 'width':
                $this->out[$fieldName] = $this->unitsManager->normalizeLength($this->object->getWidth());

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
    public function setShippingFields(string $fieldName, $fieldData): void
    {
        switch ($fieldName) {
            //====================================================================//
            // Variant Readings
            case 'weight':
                if (is_null($fieldData) || is_numeric($fieldData)) {
                    $this->setGeneric($fieldName, $this->unitsManager->convertWeight((float) $fieldData));
                }

                break;
            case 'height':
            case 'depth':
            case 'width':
                if (is_null($fieldData) || is_numeric($fieldData)) {
                    $this->setGeneric($fieldName, $this->unitsManager->convertLength((float) $fieldData));
                }

                break;
            default:
                return;
        }
        unset($this->in[$fieldName]);
    }
}

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
 * Sylius Product Stocks Fields
 */
trait StocksTrait
{
    /**
     * Build Fields using FieldFactory
     */
    public function buildStocksFields()
    {
        $groupName = "Stocks";

        //====================================================================//
        // Stock Reel
        $this->fieldsFactory()->create(SPL_T_INT)
            ->Identifier("onHand")
            ->Name("Stock on Hands")
            ->MicroData("http://schema.org/Offer", "inventoryLevel")
            ->Group($groupName)
            ->isListed();

        //====================================================================//
        // Out of Stock Flag
        $this->fieldsFactory()->create(SPL_T_BOOL)
            ->Identifier("outofstock")
            ->Name("Out of stock")
            ->MicroData("http://schema.org/ItemAvailability", "OutOfStock")
            ->Group($groupName)
            ->isReadOnly();
    }

    /**
     * Read requested Field
     *
     * @param string $key       Input List Key
     * @param string $fieldName Field Identifier / Name
     */
    public function getStocksFields($key, $fieldName)
    {
        switch ($fieldName) {
            //====================================================================//
            // Variant Readings
            case 'onHand':
                $this->getGeneric($fieldName);

                break;
            case 'outofstock':
                if ($this->object->isTracked()) {
                    $this->out[$fieldName] = ($this->object->getOnHand() > 0) ? false : true;
                }
                $this->out[$fieldName] = false;

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
    public function setStocksFields($fieldName, $fieldData)
    {
        switch ($fieldName) {
            //====================================================================//
            // Variant Writting
            case 'onHand':
                $this->setGeneric($fieldName, (int) $fieldData);

                break;
            default:
                return;
        }
        unset($this->in[$fieldName]);
    }
}

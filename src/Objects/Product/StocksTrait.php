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
 * Sylius Product Stocks Fields
 */
trait StocksTrait
{
    /**
     * Build Fields using FieldFactory
     */
    public function buildStocksFields(): void
    {
        $groupName = "Stocks";

        //====================================================================//
        // Stock Reel
        $this->fieldsFactory()->create(SPL_T_INT)
            ->identifier("onHand")
            ->name("Stock on Hands")
            ->microData("http://schema.org/Offer", "inventoryLevel")
            ->group($groupName)
            ->isListed()
        ;
        //====================================================================//
        // Out of Stock Flag
        $this->fieldsFactory()->create(SPL_T_BOOL)
            ->identifier("outofstock")
            ->name("Out of stock")
            ->microData("http://schema.org/ItemAvailability", "OutOfStock")
            ->group($groupName)
            ->isReadOnly()
        ;
    }

    /**
     * Read requested Field
     *
     * @param string $key       Input List Key
     * @param string $fieldName Field Identifier / Name
     */
    public function getStocksFields(string $key, string $fieldName): void
    {
        switch ($fieldName) {
            //====================================================================//
            // Variant Readings
            case 'onHand':
                $this->getGeneric($fieldName);

                break;
            case 'outofstock':
                $this->out[$fieldName] = false;
                if ($this->object->isTracked()) {
                    $this->out[$fieldName] = !(($this->object->getOnHand() > 0));
                }

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
    public function setStocksFields(string $fieldName, $fieldData): void
    {
        switch ($fieldName) {
            //====================================================================//
            // Variant Writing
            case 'onHand':
                $this->setGeneric($fieldName, (int) $fieldData);

                break;
            default:
                return;
        }
        unset($this->in[$fieldName]);
    }
}

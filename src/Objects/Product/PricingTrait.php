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
 * Sylius Product Prices Fields
 */
trait PricingTrait
{
    /**
     * Build Fields using FieldFactory
     */
    protected function buildPricesFields(): void
    {
        $groupName = "Pricing";

        //====================================================================//
        // PRICES INFORMATIONS
        //====================================================================//

        //====================================================================//
        // Walk on All Available Channels
        foreach ($this->pricing->getChannels() as $channel) {
            //====================================================================//
            // Generate Identifier Suffix
            $suffix = $this->pricing->getChannelSuffix($channel);

            //====================================================================//
            // Product Selling Price
            $this->fieldsFactory()->create(SPL_T_PRICE)
                ->identifier("price".$suffix)
                ->name("Price (".$channel->getCode().")")
                ->description("Selling Price Tax excl.(".$channel->getName().")")
                ->microData("http://schema.org/Product", "price".$suffix)
                ->group($groupName)
            ;
            //====================================================================//
            // WholeSale Price
            $this->fieldsFactory()->create(SPL_T_PRICE)
                ->identifier("originalPrice".$suffix)
                ->name("Wholesale Price (".$channel->getCode().")")
                ->description("Wholesale Price Tax excl.(".$channel->getName().")")
                ->group($groupName)
                ->microData("http://schema.org/Product", "wholesalePrice".$suffix)
            ;
        }
    }

    /**
     * Read requested Field
     *
     * @param null|string $key       Input List Key
     * @param string      $fieldName Field Identifier / Name
     */
    protected function getPricesFields(?string $key, string $fieldName): void
    {
        //====================================================================//
        // reduce Load By Checking Field Name
        if (false === strpos(strtolower($fieldName), "price")) {
            return;
        }
        //====================================================================//
        // Walk on All Available Channels
        foreach ($this->pricing->getChannels() as $channel) {
            //====================================================================//
            // Generate Identifier Suffix
            $suffix = $this->pricing->getChannelSuffix($channel);
            //====================================================================//
            // READ Fields
            switch ($fieldName) {
                //====================================================================//
                // PRICE INFORMATIONS
                //====================================================================//

                case 'price'.$suffix:
                    //====================================================================//
                    // Read Price
                    $this->out[$fieldName] = $this->pricing->getChannelPrice($this->object, $channel, false);
                    unset($this->in[$key]);

                    break;
                case 'originalPrice'.$suffix:
                    //====================================================================//
                    // Read Wholesale Price
                    $this->out[$fieldName] = $this->pricing->getChannelPrice($this->object, $channel, true);
                    unset($this->in[$key]);

                    break;
            }
        }
    }

    /**
     * Write Given Fields
     *
     * @param string     $fieldName Field Identifier / Name
     * @param null|array $fieldData Field Data
     */
    protected function setPricesFields(string $fieldName, ?array $fieldData): void
    {
        //====================================================================//
        // reduce Load By Checking Field Name
        if (false === strpos(strtolower($fieldName), "price")) {
            return;
        }
        //====================================================================//
        // Walk on All Available Channels
        foreach ($this->pricing->getChannels() as $channel) {
            //====================================================================//
            // Generate Identifier Suffix
            $suffix = $this->pricing->getChannelSuffix($channel);
            //====================================================================//
            // READ Fields
            switch ($fieldName) {
                //====================================================================//
                // PRICE INFORMATIONS
                //====================================================================//

                case 'price'.$suffix:
                    //====================================================================//
                    // Write Price
                    $updated = $this->pricing->setChannelPrice($this->object, $channel, false, $fieldData);
                    unset($this->in[$fieldName]);
                    if ($updated) {
                        $this->needUpdate('product');
                    }

                    break;
                case 'originalPrice'.$suffix:
                    //====================================================================//
                    // Write Wholesale Price
                    $updated = $this->pricing->setChannelPrice($this->object, $channel, true, $fieldData);
                    unset($this->in[$fieldName]);
                    if ($updated) {
                        $this->needUpdate('product');
                    }

                    break;
            }
        }
    }
}

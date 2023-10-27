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
 * Sylius Product Taxes Fields
 */
trait TaxesTrait
{
    /**
     * Build Fields using FieldFactory
     */
    protected function buildTaxesFields(): void
    {
        $groupName = "Pricing";

        //====================================================================//
        // PRICES INFORMATIONS
        //====================================================================//

        //====================================================================//
        // Product Default Tax Category
        $this->fieldsFactory()->create(SPL_T_VARCHAR)
            ->identifier("tax_category")
            ->name("Tax Category")
            ->description("Product Tax Category ")
            ->group($groupName)
            ->isReadOnly()
        ;
        //====================================================================//
        // Product Default Tax Rate
        $this->fieldsFactory()->create(SPL_T_VARCHAR)
            ->identifier("default_tax_rate")
            ->name("Default Tax")
            ->description("Default Tax Rate")
            ->group($groupName)
            ->isReadOnly()
        ;
        //====================================================================//
        // Product Default Tax Rate is Included
        $this->fieldsFactory()->create(SPL_T_BOOL)
            ->identifier("default_tax_rate_included")
            ->name("Tax Included")
            ->description("Default Tax Rate is Included in Price")
            ->group($groupName)
            ->isReadOnly()
        ;
    }

    /**
     * Read requested Field
     *
     * @param null|string $key       Input List Key
     * @param string      $fieldName Field Identifier / Name
     */
    protected function getTaxesFields(?string $key, string $fieldName): void
    {
        //====================================================================//
        // READ Fields
        switch ($fieldName) {
            case 'tax_category':
                $taxCategory = $this->object->getTaxCategory();
                $this->out[$fieldName] = $taxCategory
                    ? sprintf("[%s] %s", $taxCategory->getCode(), $taxCategory->getName())
                    : null
                ;

                break;
            case 'default_tax_rate':
                $taxRate = $this->pricing->getDefaultRate($this->object);
                $this->out[$fieldName] = $taxRate ? $taxRate->getName() : null;

                break;
            case 'default_tax_rate_included':
                $taxRate = $this->pricing->getDefaultRate($this->object);
                $this->out[$fieldName] = $taxRate ? $taxRate->isIncludedInPrice() : false;

                break;
            default:
                return;
        }
        unset($this->in[$key]);
    }
}

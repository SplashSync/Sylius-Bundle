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

namespace Splash\Sylius\Objects\Order;

/**
 * Sylius Customer Order Totals Field
 */
trait TotalsTrait
{
    /**
     * Build Fields using FieldFactory
     */
    public function buildTotalsFields()
    {
        //====================================================================//
        // Order Total Amount
        $this->fieldsFactory()->Create(SPL_T_DOUBLE)
            ->Identifier("total")
            ->Name("Total Tax Excl.")
            ->isReadOnly();

        //====================================================================//
        // Order Currency Code
        $this->fieldsFactory()->create(SPL_T_CURRENCY)
            ->Identifier("currencyCode")
            ->Name("Currency")
            ->isReadOnly();
    }

    /**
     * Read requested Field
     *
     * @param string $key       Input List Key
     * @param string $fieldName Field Identifier / Name
     */
    private function getTotalsFields($key, $fieldName)
    {
        //====================================================================//
        // READ Fields
        switch ($fieldName) {
            //====================================================================//
            // Direct Readings
            case 'currencyCode':
                $this->getGeneric($fieldName);

                break;
            //====================================================================//
            // Order Total Amount
            case 'total':
                $this->out[$fieldName] = doubleval($this->object->getTotal() / 100);

                break;
            default:
                return;
        }
        unset($this->in[$key]);
    }
}

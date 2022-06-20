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

namespace Splash\SyliusSplashPlugin\Objects\Order;

/**
 * Sylius Customer Order Totals Field
 */
trait TotalsTrait
{
    /**
     * Build Fields using FieldFactory
     */
    public function buildTotalsFields(): void
    {
        //====================================================================//
        // Order Total Amount
        $this->fieldsFactory()->Create(SPL_T_DOUBLE)
            ->identifier("total")
            ->name("Total Tax Excl.")
            ->isReadOnly()
        ;
        //====================================================================//
        // Order Currency Code
        $this->fieldsFactory()->create(SPL_T_CURRENCY)
            ->identifier("currencyCode")
            ->name("Currency")
            ->isReadOnly()
        ;
    }

    /**
     * Read requested Field
     *
     * @param string $key       Input List Key
     * @param string $fieldName Field Identifier / Name
     */
    private function getTotalsFields(string $key, string $fieldName): void
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

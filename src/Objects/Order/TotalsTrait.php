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
        // Order Total Price
        $this->fieldsFactory()->create(SPL_T_PRICE)
            ->identifier("price_total")
            ->name("Order Total")
            ->microData("http://schema.org/Invoice", "total")
            ->group("Totals")
            ->isReadOnly()
        ;
        //====================================================================//
        // Order Total Shipping
        $this->fieldsFactory()->create(SPL_T_PRICE)
            ->identifier("price_shipping")
            ->name("Order Shipping")
            ->microData("http://schema.org/Invoice", "totalShipping")
            ->group("Totals")
            ->isReadOnly()
        ;
        //====================================================================//
        // Order Total Shipping
        $this->fieldsFactory()->create(SPL_T_PRICE)
            ->identifier("price_discount")
            ->name("Order Discounts")
            ->microData("http://schema.org/Invoice", "totalDiscount")
            ->group("Totals")
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
            case 'price_total':
                $orderTotal = $this->object->getTotal();
                $this->out[$fieldName] = self::prices()->encode(
                    null,
                    self::toVatPercents($orderTotal - $this->object->getTaxTotal(), $orderTotal),
                    (double) ($orderTotal / 100),
                    $this->object->getCurrencyCode() ?? "USD",
                );

                break;
            case 'price_shipping':
                $this->out[$fieldName] = self::prices()->encode(
                    (double) ($this->object->getShippingTotal() / 100),
                    0.0,
                    null,
                    $this->object->getCurrencyCode() ?? "USD",
                );

                break;
            case 'price_discount':
                $this->out[$fieldName] = self::prices()->encode(
                    (double) abs($this->object->getOrderPromotionTotal() / 100),
                    0.0,
                    null,
                    $this->object->getCurrencyCode() ?? "USD",
                );

                break;
            case 'currencyCode':
                $this->getGeneric($fieldName);

                break;
            default:
                return;
        }
        unset($this->in[$key]);
    }

    /**
     * Compute Vat Percentile from Both Price Values
     *
     * @param float $priceTaxExcl
     * @param float $priceTaxIncl
     *
     * @return float
     */
    private static function toVatPercents(float $priceTaxExcl, float $priceTaxIncl): float
    {
        return (($priceTaxExcl > 0) && ($priceTaxIncl > 0) && ($priceTaxExcl <= $priceTaxIncl))
            ? 100 * ($priceTaxIncl - $priceTaxExcl) / $priceTaxExcl
            : 0.0
        ;
    }
}

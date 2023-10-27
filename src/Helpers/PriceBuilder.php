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

namespace Splash\SyliusSplashPlugin\Helpers;

use Splash\Models\Objects\PricesTrait;
use Sylius\Component\Taxation\Model\TaxRateInterface;

/**
 * Tooling Helper for Building Splash formatted Price Array
 */
class PriceBuilder
{
    use PricesTrait;

    public static function toPrice(int $price, string $currency, ?TaxRateInterface $taxRate): ?array
    {
        //====================================================================//
        // Detect Tax Rate
        $vatRate = $taxRate ? $taxRate->getAmountAsPercentage() : 0.0;
        $taxIncl = $taxRate && $taxRate->isIncludedInPrice();

        //====================================================================//
        // Encode Splash Price Array
        return self::prices()->encode(
            // Tax Excluded Price
            $taxIncl ? null : doubleval($price / 100),
            // TAX Percent
            $vatRate,
            // Tax Excluded Price
            $taxIncl ? doubleval($price / 100) : null,
            $currency,
            $currency,
            $currency
        );
    }
}

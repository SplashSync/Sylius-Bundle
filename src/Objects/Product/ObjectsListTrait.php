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

use Sylius\Component\Core\Model\ProductVariantInterface;
use Splash\Client\Splash;

/**
 * Sylius Product Objects Lists
 */
trait ObjectsListTrait
{
    use \Splash\Bundle\Helpers\Doctrine\ObjectsListHelperTrait;

    /**
     * Transform Product To List Array Data
     *
     * @param ProductVariantInterface $variant
     *
     * @return array
     */
    protected function getObjectListArray(ProductVariantInterface $variant): array
    {
        $product = $variant->getProduct();

        return array(
            'id' => $variant->getId(),
            'code' => $variant->getCode(),
            'enabled' => $product ? $product->isEnabled() : false,
            'email' => $variant->getName(),
            'phoneNumber' => $variant->getName(),
            'onHand' => $variant->getOnHand(),
        );
    }
}

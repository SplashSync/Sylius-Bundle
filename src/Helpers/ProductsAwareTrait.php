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

namespace Splash\Sylius\Helpers;

use Sylius\Bundle\ProductBundle\Doctrine\ORM\ProductRepository;

/**
 * Give Access to Products for Objects
 */
class ProductsAwareTrait
{
    /**
     * @var ProductRepository
     */
    private $products;

    /**
     * Setup Products Repository
     *
     * @param ProductRepository $repository
     *
     * @return $this
     */
    protected function setProductsRepository(ProductRepository $repository): self
    {
        $this->products = $repository;
    }
}

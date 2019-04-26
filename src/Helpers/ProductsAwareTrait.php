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

use Sylius\Bundle\CoreBundle\Doctrine\ORM\ProductsRepository;

/**
 * Give Access to Products for Objects
 */
class ProdutsAwareTrait
{
    /**
     * @var ProductsRepository
     */
    private $products;

    /**
     * Setup Products Repository
     *
     * @param ProductsRepository $repository
     *
     * @return $this
     */
    protected function setChannelsRepository(ProductsRepository $repository): self
    {
        $this->products = $repository;
    }
}

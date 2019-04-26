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

use Sylius\Component\Core\Model\OrderInterface;

/**
 * Sylius Order CRUD
 */
trait CrudTrait
{
    use \Splash\Sylius\Helpers\Doctrine\CrudTrait;

    /**
     * Create Request Object
     *
     * @return false|OrderInterface
     */
    public function create()
    {
        //====================================================================//
        // Load Default Channel
        $dfChannel = $this->getDefaultChannel();       
        if (empty($dfChannel)) {
            return false;
        }
        //====================================================================//
        // Create a New Object
        /** @var OrderInterface $order */
        $order = $this->factory->createNew();
        $order->setChannel($dfChannel);
        $dfLocale = $dfChannel->getDefaultLocale();
        if ($dfLocale) {
            $order->setLocaleCode($dfLocale->getCode());
        }
        $dfCurrency = $dfChannel->getBaseCurrency();
        if ($dfCurrency) {
            $order->setCurrencyCode($dfCurrency->getCode());
        }
        //====================================================================//
        // Persist New Object
        $this->repository->add($order);
        //====================================================================//
        // Return a New Object
        return  $order;
    }
}

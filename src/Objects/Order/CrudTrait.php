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

use Exception;
use Sylius\Component\Core\Model\OrderInterface;

/**
 * Sylius Order CRUD
 */
trait CrudTrait
{
    use \Splash\SyliusSplashPlugin\Helpers\Doctrine\CrudTrait;

    /**
     * Create Request Object
     *
     * @throws Exception
     *
     * @return null|OrderInterface
     */
    public function create(): ?OrderInterface
    {
        //====================================================================//
        // Load Default Channel
        $dfChannel = $this->getDefaultChannel();
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

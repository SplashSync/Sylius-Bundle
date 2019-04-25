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
 * Sylius Orders Objects Lists
 */
trait ObjectsListTrait
{
    use \Splash\Bundle\Helpers\Doctrine\ObjectsListHelperTrait;

    /**
     * Transform Order To List Array Data
     *
     * @param OrderInterface $order
     *
     * @return array
     */
    protected function getObjectListArray(OrderInterface $order): array
    {
        $orderDate = $order->getCheckoutCompletedAt();
        $orderDateStr = $orderDate ? $orderDate->format(SPL_T_DATECAST) : "";
        
        return array(
            'id' => $order->getId(),
            'number' => $order->getNumber(),
            'checkoutCompletedAt' => $orderDateStr,
        );
    }
}

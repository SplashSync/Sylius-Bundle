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

use Doctrine\ORM\QueryBuilder;
use Splash\Bundle\Helpers\Doctrine\ObjectsListHelperTrait;
use Sylius\Component\Core\Model\OrderInterface;

/**
 * Sylius Orders Objects Lists
 */
trait ObjectsListTrait
{
    use ObjectsListHelperTrait;

    /**
     * Configure List Query Builder
     */
    protected function configureObjectListQueryBuilder(QueryBuilder $queryBuilder): void
    {
        //====================================================================//
        // Change Sort Order to See Last orders First
        $queryBuilder->addOrderBy('c.createdAt', "DESC");
    }

    /**
     * Configure List Query Builder Filters
     */
    protected function setObjectListFilter(QueryBuilder $queryBuilder, string $filter): void
    {
        $orx = $queryBuilder->expr()->orX();
        $orx->add($queryBuilder->expr()->like('c.createdAt', ":textFilter"));
        $orx->add($queryBuilder->expr()->like('c.checkoutCompletedAt', ":textFilter"));
        $orx->add($queryBuilder->expr()->eq('c.number', $filter));
        $queryBuilder->andWhere($orx);
        $queryBuilder->setParameter('textFilter', "%".$filter."%");
    }

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

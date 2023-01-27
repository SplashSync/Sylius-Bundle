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

use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\NonUniqueResultException;

/**
 * Identify Order by Primary Key (Number)
 */
trait PrimaryTrait
{
    /**
     * @inheritDoc
     */
    public function getByPrimary(array $keys): ?string
    {
        //====================================================================//
        // Extract Primary Key
        $number = $keys['number'] ?? null;
        if (empty($number) || !is_string($number)) {
            return null;
        }
        //====================================================================//
        // Find by Number
        try {
            /** @var null|array $order */
            $order = $this->repository->createQueryBuilder('o')
                ->andWhere('o.number = :number')
                ->setParameter('number', $number)
                ->getQuery()
                ->getOneOrNullResult(AbstractQuery::HYDRATE_ARRAY);
        } catch (NonUniqueResultException $e) {
            return null;
        }
        //====================================================================//
        // Verify results
        if (empty($order['id']) || !is_scalar($order['id'])) {
            return null;
        }

        return (string) $order["id"];
    }
}

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

namespace Splash\SyliusSplashPlugin\Objects\ThirdParty;

use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\NonUniqueResultException;

/**
 * Identify Customer by Primary Key (SKU)
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
        $email = $keys['email'] ?? null;
        if (empty($email) || !is_string($email)) {
            return null;
        }

        //====================================================================//
        // Find by Email
        try {
            /** @var null|array $customer */
            $customer = $this->repository->createQueryBuilder('o')
                ->andWhere('o.email = :email')
                ->setParameter('email', $email)
                ->getQuery()
                ->getOneOrNullResult(AbstractQuery::HYDRATE_ARRAY);
        } catch (NonUniqueResultException $e) {
            return null;
        }
        //====================================================================//
        // Verify results
        if (empty($customer['id']) || !is_scalar($customer['id'])) {
            return null;
        }

        return (string) $customer["id"];
    }
}

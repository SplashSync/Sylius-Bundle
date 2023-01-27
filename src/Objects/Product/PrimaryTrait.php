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

namespace Splash\SyliusSplashPlugin\Objects\Product;

use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\NonUniqueResultException;

/**
 * Identify Product Variant by Primary Key (SKU)
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
        $variantCode = $keys['code'] ?? null;
        if (empty($variantCode) || !is_string($variantCode)) {
            return null;
        }
        //====================================================================//
        // Find by Variant Code
        try {
            /** @var null|array $variant */
            $variant = $this->repository->createQueryBuilder('o')
                ->andWhere('o.code = :code')
                ->setParameter('code', $variantCode)
                ->getQuery()
                ->getOneOrNullResult(AbstractQuery::HYDRATE_ARRAY);
        } catch (NonUniqueResultException $e) {
            return null;
        }
        //====================================================================//
        // Verify results
        if (empty($variant['id']) || !is_scalar($variant['id'])) {
            return null;
        }

        return (string) $variant["id"];
    }
}

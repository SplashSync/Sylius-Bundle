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

namespace Splash\Sylius\Models;

use Splash\Client\Splash;
use Doctrine\ORM\QueryBuilder;

/**
 * Generic Doctrine Object List Helps
 */
trait ObjectListHelperTrait
{
    /**
     * Return List Of Objects with required filters
     *
     * @param string $filter Filters for Object List.
     * @param array  $params Search parameters for result List.
     *
     * @return array
     */
    public function objectsList($filter = null, $params = null)
    {
        //====================================================================//
        // Stack Trace
        Splash::log()->trace();

        //====================================================================//
        // Prepare Query Builder
        /** @var QueryBuilder $queryBuilder */
        $queryBuilder = $this->repository->createQueryBuilder('c');
        // Setup Results Offset
        if (isset($params['offset']) && is_int($params['offset'])) {
            $queryBuilder->setFirstResult($params['offset']);
        }
        // Limit Results Number
        if (isset($params['max']) && is_int($params['max'])) {
            $queryBuilder->setMaxResults($params['max']);
        }

        //====================================================================//
        // Add List Filters
        if (!empty($filter)) {
        }

        //====================================================================//
        // Load Objects List
        $rawData = $queryBuilder->getQuery()->getResult();

        //====================================================================//
        // Parse Data on Result Array
        $response = array();
        foreach ($rawData as $object) {
            $response[] = $this->getObjectListArray($object);
        }

        //====================================================================//
        // Parse Meta Infos on Result Array
        $response['meta'] = array(
            'total' => count($rawData),
            'current' => $this->getTotalCount(),
        );

        //====================================================================//
        // Return result
        return $response;
    }

    /**
     * Get Total Object Count
     *
     * @return int
     */
    private function getTotalCount(): int
    {
        return $this->repository->createQueryBuilder('u')
            ->select('count(u.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }
}

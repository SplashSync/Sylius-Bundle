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

namespace Splash\Sylius\Helpers\Doctrine;

use Doctrine\ORM\EntityManagerInterface;
use Splash\Client\Splash;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * Generic Doctrine Object Crud Helps
 */
trait CrudTrait
{
    /**
     * Doctrine Entity Manager
     *
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * @var RepositoryInterface
     */
    protected $repository;

    /**
     * Load Request Object
     *
     * @param string $objectId Object id
     *
     * @return false|object
     */
    public function load($objectId)
    {
        //====================================================================//
        // Stack Trace
        Splash::log()->trace();
        //====================================================================//
        // Search in Repository
        $entity = $this->repository->find($objectId);
        //====================================================================//
        // Check Object Entity was Found
        if (empty($entity)) {
            return Splash::log()->errTrace(static::$NAME.' : Unable to load '.$objectId);
        }

        return $entity;
    }

    /**
     * Update Request Object
     *
     * @param array $needed Is This Update Needed
     *
     * @return string Object Id
     */
    public function update($needed)
    {
        //====================================================================//
        // Save
        if ($needed) {
            $this->entityManager->flush();
        }

        return $this->getObjectIdentifier();
    }

    /**
     * {@inheritdoc}
     */
    public function delete($objectId = null)
    {
        //====================================================================//
        // Try Loading Object to Check if Exists
        $this->object = $this->load($objectId);
        if ($this->object) {
            //====================================================================//
            // Delete
            $this->repository->remove($this->object);
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getObjectIdentifier()
    {
        if (empty($this->object)) {
            return false;
        }

        return (string) $this->object->getId();
    }
}

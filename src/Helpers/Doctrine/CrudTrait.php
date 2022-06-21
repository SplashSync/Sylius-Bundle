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

namespace Splash\SyliusSplashPlugin\Helpers\Doctrine;

use Doctrine\ORM\EntityManagerInterface;
use Splash\Client\Splash;
use Sylius\Component\Resource\Model\ResourceInterface;
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
    protected EntityManagerInterface $entityManager;

    /**
     * @var RepositoryInterface
     */
    protected RepositoryInterface $repository;

    /**
     * Load Request Object
     *
     * @param string $objectId Object id
     *
     * @return null|ResourceInterface
     */
    public function load(string $objectId): ?ResourceInterface
    {
        //====================================================================//
        // Stack Trace
        Splash::log()->trace();
        //====================================================================//
        // Search in Repository
        $entity = $this->repository->find($objectId);
        //====================================================================//
        // Check Object Entity was Found
        if (!$entity instanceof ResourceInterface) {
            return Splash::log()->errNull(static::$name.' : Unable to load '.$objectId);
        }

        return $entity;
    }

    /**
     * Update Request Object
     *
     * @param bool $needed Is This Update Needed
     *
     * @return null|string Object ID
     */
    public function update(bool $needed): ?string
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
    public function delete(string $objectId): bool
    {
        //====================================================================//
        // Try Loading Object to Check if Exists
        $object = $this->load($objectId);
        if ($object) {
            //====================================================================//
            // Delete
            $this->repository->remove($object);
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getObjectIdentifier(): ?string
    {
        if (empty($this->object)) {
            return null;
        }

        return (string) $this->object->getId();
    }
}

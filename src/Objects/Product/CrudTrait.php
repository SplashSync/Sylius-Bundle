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

namespace Splash\Sylius\Objects\Product;

use Splash\Client\Splash;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Doctrine\ORM\EntityManagerInterface;
use Sylius\Bundle\CoreBundle\Doctrine\ORM\ProductVariantRepository;

/**
 * Sylius Product CRUD
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
     * @var ProductVariantRepository
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
        if (!$entity) {
            return Splash::log()->errTrace(static::$NAME.' : Unable to load '.$objectId);
        }
        //====================================================================//
        // Load Parent Product Entity
        $this->product = $entity->getProduct();
//        //====================================================================//
//        // Load Parent Product Translations
//        $this->translations = $entity->getProduct()->getTranslations();
        
        return $entity;
    }
    /**
     * Create Request Object
     *
     * @return ProductVariantInterface|false
     */
    public function create()
    {
        //====================================================================//
        // Stack Trace
        Splash::log()->trace();

        //====================================================================//
        // Check Customer Email is given
        if (empty($this->in["email"])) {
            return Splash::log()->err("ErrLocalFieldMissing", __CLASS__, __FUNCTION__, "email");
        }
        
        //====================================================================//
        // Create New Entity
        /** @var CustomerInterface $customer */
        $customer = $this->factory->createNew();
        $customer->setEmail($this->in["email"]);
        $this->repository->add($customer);

        return $customer;
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
            $this->entityManager->flush($this->object);
        }

        if ($this->isUpdated("product")) {
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
        if (!$this->object) {
            return true;
        }
        //====================================================================//
        // Load Product from Variant
        $product    =   $this->object->getProduct();
        //====================================================================//
        // Delete Product Variant from Product
        $product->removeVariant($this->object);
        //====================================================================//
        // If Product has no more Variant
        if ($product->getVariants()->count() == 0) {
            //====================================================================//
            // Delete
            $this->repository->remove($product);
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
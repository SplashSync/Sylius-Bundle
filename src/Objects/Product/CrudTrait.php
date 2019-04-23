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
        $variant = $this->crud->loadVariant($objectId);
        //====================================================================//
        // Check Object Entity was Found
        if (null == $variant) {
            return false;
        }
        //====================================================================//
        // Load Parent Product Entity
        $this->product = $variant->getProduct();
        
        return $variant;
    }
    
    /**
     * Create Request Object
     *
     * @return ProductVariantInterface|false
     */
    public function create()
    {
        //====================================================================//
        // Create New Product Variant Entity
        $variant = $this->crud->createVariant($this->in);
        if (null == $variant) {
            return false;
        }
        //====================================================================//
        // Load Parent Product Entity
        $this->product = $variant->getProduct();
        return  $variant;        
        
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
        $this->crud->update($this->object, (bool) $needed, (bool) $this->isUpdated("product"));
        
        //====================================================================//
        // Return Object Id
        return $this->getObjectIdentifier();
    }
    
    /**
     * {@inheritdoc}
     */
    public function delete($objectId = null)
    {
        return $this->crud->delete($objectId);
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
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

use Splash\Client\Splash;
use Sylius\Bundle\CoreBundle\Doctrine\ORM\ProductVariantRepository;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Product\Model\ProductVariantInterface as Variant;

/**
 * Sylius Product CRUD
 */
trait CrudTrait
{
    /**
     * @var ProductVariantRepository
     */
    protected ProductVariantRepository $repository;

    /**
     * Load Request Object
     *
     * @param string $objectId Object id
     *
     * @return null|Variant
     */
    public function load(string $objectId): ?Variant
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
            return null;
        }
        //====================================================================//
        // Load Parent Product Entity
        $product = $variant->getProduct();
        if ($product instanceof ProductInterface) {
            $this->product = $product;
        }

        return $variant;
    }

    /**
     * Create Request Object
     *
     * @return null|Variant
     */
    public function create()
    {
        //====================================================================//
        // Create New Product Variant Entity
        $variant = $this->crud->createVariant($this->in);
        if (!($variant instanceof Variant)) {
            return null;
        }
        //====================================================================//
        // Load Parent Product Entity
        $product = $variant->getProduct();
        if ($product instanceof ProductInterface) {
            $this->product = $product;
        }

        return  $variant;
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
        $this->crud->update((bool) $needed, (bool) $this->isUpdated("product"));

        //====================================================================//
        // Return Object Id
        return $this->getObjectIdentifier();
    }

    /**
     * {@inheritdoc}
     */
    public function delete(string $objectId): bool
    {
        return $this->crud->delete($objectId);
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

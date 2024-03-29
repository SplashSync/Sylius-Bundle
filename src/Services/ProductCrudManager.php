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

namespace   Splash\SyliusSplashPlugin\Services;

use ArrayObject;
use Doctrine\ORM\EntityManagerInterface as Manager;
use Splash\Core\SplashCore      as Splash;
use Splash\Models\Objects\ObjectsTrait;
use Splash\SyliusSplashPlugin\Helpers\ChannelsAwareTrait;
use Sylius\Bundle\ChannelBundle\Doctrine\ORM\ChannelRepository as Channels;
use Sylius\Bundle\CoreBundle\Doctrine\ORM\ProductRepository as Products;
use Sylius\Bundle\CoreBundle\Doctrine\ORM\ProductVariantRepository as Variants;
use Sylius\Component\Core\Model\ProductInterface as Product;
use Sylius\Component\Product\Factory\ProductFactory;
use Sylius\Component\Product\Factory\ProductVariantFactory;
use Sylius\Component\Product\Model\ProductVariantInterface as Variant;

/**
 * Product CRUD Manager
 * Manage Create / Load / Update / Delete for Products Variants
 */
class ProductCrudManager
{
    use ObjectsTrait;
    use ChannelsAwareTrait;

    /**
     * Doctrine Entity Manager
     *
     * @var Manager
     */
    protected Manager $entityManager;

    /**
     * @var Products
     */
    protected Products $products;

    /**
     * @var Variants
     */
    protected Variants $variants;

    /**
     * @var ProductFactory
     */
    protected ProductFactory $productFactory;

    /**
     * @var ProductVariantFactory
     */
    protected ProductVariantFactory $variantFactory;

    /**
     * @var array
     */
    protected array $config;

    /**
     * Service Constructor
     *
     * @param Manager               $manager
     * @param Products              $products
     * @param Variants              $variants
     * @param Channels              $channels
     * @param ProductFactory        $pFactory
     * @param ProductVariantFactory $vFactory
     * @param array                 $configuration
     */
    public function __construct(
        Manager $manager,
        Products $products,
        Variants $variants,
        Channels $channels,
        ProductFactory $pFactory,
        ProductVariantFactory $vFactory,
        array $configuration
    ) {
        //====================================================================//
        // Sylius Product Manager
        $this->entityManager = $manager;
        //====================================================================//
        // Sylius Products Repository
        $this->products = $products;
        //====================================================================//
        // Sylius Products Variants Repository
        $this->variants = $variants;
        //====================================================================//
        // Sylius Products Factory
        $this->productFactory = $pFactory;
        //====================================================================//
        // Sylius Products Variant Factory
        $this->variantFactory = $vFactory;
        //====================================================================//
        // Store Bundle Configuration
        $this->config = $configuration;
        //====================================================================//
        // Setup Sylius Channels Repository
        $this->setChannelsRepository($channels, $configuration);
    }

    /**
     * Load Product Variant
     *
     * @param string $objectId Product Variant Id
     *
     * @return null|Variant
     */
    public function loadVariant(string $objectId): ?Variant
    {
        //====================================================================//
        // Search in Repository
        $variant = $this->variants->find($objectId);
        //====================================================================//
        // Check Object Entity was Found
        if (!$variant instanceof Variant) {
            Splash::log()->errTrace('Unable to load Product variant '.$objectId);

            return null;
        }

        return $variant;
    }

    /**
     * Create Request Object
     *
     * @param array|ArrayObject $inputs Objects Input Array
     *
     * @return null|Variant
     */
    public function createVariant($inputs): ?Variant
    {
        //====================================================================//
        // Stack Trace
        Splash::log()->trace();
        //====================================================================//
        // Check Required Fields are given
        if (!$this->isReadyToCreate($inputs)) {
            return null;
        }
        //====================================================================//
        // Load Variant Base Product if Found
        $baseProduct = $this->getBaseProduct($inputs);
        if ($baseProduct instanceof Product) {
            //====================================================================//
            // Create a Variant
            /** @var Variant $variant */
            $variant = $this->variantFactory->createForProduct($baseProduct);
            $baseProduct->addVariant($variant);
            //====================================================================//
            // Persist New Variant
            $this->entityManager->persist($variant);

            //====================================================================//
            // Return New Variant
            return $variant;
        }
        //====================================================================//
        // Create a Product
        /** @var Product $newProduct */
        $newProduct = $this->productFactory->createWithVariant();
        //====================================================================//
        // Setup Product Channel
        $newProduct->addChannel($this->getDefaultChannel());
        //====================================================================//
        // Persist New Product
        $this->entityManager->persist($newProduct);

        //====================================================================//
        // Return a Product First Variant
        return  $newProduct->getVariants()->first() ?: null;
    }

    /**
     * Update Request Object
     *
     * @param bool $needed        Is This Variant Update Needed
     * @param bool $neededProduct Is This Product Update Needed
     */
    public function update(bool $needed, bool $neededProduct): void
    {
        //====================================================================//
        // Save All Product Changes
        if ($needed || $neededProduct) {
            $this->entityManager->flush();
        }
    }

    /**
     * Delete Product Variant & Prodcut if Last Variant
     *
     * @param string $objectId
     *
     * @return bool
     */
    public function delete(string $objectId = null): bool
    {
        //====================================================================//
        // Try Loading Object to Check if Exists
        $variant = $this->loadVariant((string) $objectId);
        if (!$variant) {
            return true;
        }
        //====================================================================//
        // Load Product from Variant
        $product = $variant->getProduct();
        if (!$product) {
            return false;
        }
        //====================================================================//
        // Delete Product Variant from Product
        $product->removeVariant($variant);
        $this->variants->remove($variant);
        //====================================================================//
        // If Product has no more Variant
        if (0 == $product->getVariants()->count()) {
            //====================================================================//
            // Delete
            $this->products->remove($product);
        }

        return true;
    }

    /**
     * Check if Enough Inputs for Create
     *
     * @param array|ArrayObject $inputs Objects Input Array
     *
     * @return bool
     */
    private function isReadyToCreate($inputs): bool
    {
        //====================================================================//
        // Check Product SKU / Code is given
        if (empty($inputs["code"])) {
            return Splash::log()->err("ErrLocalFieldMissing", __CLASS__, __FUNCTION__, "code");
        }
        //====================================================================//
        // Check Product Name is given
        if (empty($inputs["name"])) {
            return Splash::log()->err("ErrLocalFieldMissing", __CLASS__, __FUNCTION__, "name");
        }

        return true;
    }

    /**
     * Search for Base Product by Given Existing Variants Ids
     *
     * @param array|ArrayObject $inputs Objects Input Array
     *
     * @return null|Product
     */
    private function getBaseProduct($inputs): ?Product
    {
        //====================================================================//
        // Stack Trace
        Splash::log()->trace();
        //====================================================================//
        // Check Variants Array is Here
        if (!isset($inputs["variants"])) {
            return null;
        }
        $variants = $inputs["variants"];
        //====================================================================//
        // Check Name is Array
        if (!is_iterable($variants) || empty($variants)) {
            return null;
        }
        //====================================================================//
        // For Each Available Variants
        foreach ($variants as $variant) {
            //====================================================================//
            // Check Product Searched Product
            $baseProduct = $this->isBaseProduct($variant);
            if ($baseProduct instanceof Product) {
                return $baseProduct;
            }
        }

        return null;
    }

    /**
     * Is Base Product by Given Existing Variants Ids
     *
     * @param array|ArrayObject $variant Variant Input Array
     *
     * @return null|Product
     */
    private function isBaseProduct($variant): ?Product
    {
        //====================================================================//
        // Check Product Id is here
        if (!isset($variant["id"]) || !is_string($variant["id"])) {
            return null;
        }
        //====================================================================//
        // Extract Variable Product Id
        $baseVariantId = self::objects()->id($variant["id"]);
        if (!$baseVariantId) {
            return null;
        }
        //====================================================================//
        // Load Base Product
        $baseVariant = $this->variants->find($baseVariantId);
        if (!($baseVariant instanceof Variant)) {
            return null;
        }
        $baseProduct = $baseVariant->getProduct();
        if (!($baseProduct instanceof Product)) {
            return null;
        }

        return $baseProduct;
    }
}

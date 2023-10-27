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

namespace Splash\SyliusSplashPlugin\Objects;

use Splash\Bundle\Models\AbstractStandaloneObject;
use Splash\Models\Objects\GenericFieldsTrait;
use Splash\Models\Objects\IntelParserTrait;
use Splash\Models\Objects\ListsTrait;
use Splash\Models\Objects\PrimaryKeysAwareInterface;
use Splash\Models\Objects\SimpleFieldsTrait;
use Splash\SyliusSplashPlugin\Services\ProductAttributesManager as Attributes;
use Splash\SyliusSplashPlugin\Services\ProductCrudManager as Crud;
use Splash\SyliusSplashPlugin\Services\ProductImagesManager as Images;
use Splash\SyliusSplashPlugin\Services\ProductPricingManager as Pricing;
use Splash\SyliusSplashPlugin\Services\ProductTranslationsManager as Translations;
use Sylius\Bundle\CoreBundle\Doctrine\ORM\ProductVariantRepository as Variants;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Product\Factory\ProductFactory as Factory;

/**
 * Sylius Product Object
 */
class Product extends AbstractStandaloneObject implements PrimaryKeysAwareInterface
{
    // Splash Php Core Traits
    use IntelParserTrait;
    use SimpleFieldsTrait;
    use ListsTrait;
    use GenericFieldsTrait;

    // Product Traits
    use Product\CrudTrait;
    use Product\PrimaryTrait;
    use Product\ObjectsListTrait;
    use Product\CoreTrait;
    use Product\ShippingTrait;
    use Product\DescriptionsTrait;
    use Product\PricingTrait;
    use Product\TaxesTrait;
    use Product\StocksTrait;
    use Product\CoverImageTrait;
    use Product\ImagesTrait;

    // Products Variants Traits
    use Product\Variants\CoreTrait;
    use Product\Variants\AttributesTrait;

    //====================================================================//
    // Object Definition Parameters
    //====================================================================//

    /**
     * {@inheritdoc}
     */
    protected static string $name = "Product";

    /**
     * {@inheritdoc}
     */
    protected static string $description = 'Sylius Product Object';

    /**
     * {@inheritdoc}
     */
    protected static string $ico = 'fa fa-product-hunt';

    /**
     * {@inheritdoc}
     */
    protected static bool $enablePushCreated = false;

    //====================================================================//
    // Private variables
    //====================================================================//

    /**
     * @phpstan-var ProductVariantInterface
     */
    protected object $object;

    /**
     * @var ProductInterface
     */
    protected $product;

    /**
     * @var Crud
     */
    protected Crud $crud;

    /**
     * @var Translations
     */
    protected Translations $translations;

    /**
     * @var Images
     */
    protected Images $images;

    /**
     * @var Pricing
     */
    protected Pricing $pricing;

    /**
     * @var Attributes
     */
    protected Attributes $attributes;

    /**
     * @var Factory
     */
    protected Factory $factory;

    //====================================================================//
    // Service Constructor
    //====================================================================//

    /**
     * Service Constructor
     *
     * @param Variants     $variants
     * @param Crud         $crudService
     * @param Translations $translations
     * @param Images       $images
     * @param Pricing      $pricing
     * @param Attributes   $attributes
     */
    public function __construct(
        Variants $variants,
        Crud $crudService,
        Translations $translations,
        Images $images,
        Pricing $pricing,
        Attributes $attributes
    ) {
        //====================================================================//
        // Link to Product Variants Repository
        $this->repository = $variants;
        //====================================================================//
        // Link to Splash Sylius Products Crud Manager
        $this->crud = $crudService;
        //====================================================================//
        // Link to Splash Sylius Translations Manager
        $this->translations = $translations;
        //====================================================================//
        // Link to Splash Sylius Images Manager
        $this->images = $images;
        //====================================================================//
        // Link to Splash Sylius Channel Pricing Manager
        $this->pricing = $pricing;
        //====================================================================//
        // Link to Splash Sylius Products Attributes Manager
        $this->attributes = $attributes;
    }
}

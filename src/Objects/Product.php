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

namespace Splash\Sylius\Objects;

use Splash\Bundle\Models\AbstractStandaloneObject;
use Splash\Models\Objects\GenericFieldsTrait;
use Splash\Models\Objects\IntelParserTrait;
use Splash\Models\Objects\ListsTrait;
use Splash\Models\Objects\SimpleFieldsTrait;
use Splash\Sylius\Services\ProductAttributesManager as Attributes;
use Splash\Sylius\Services\ProductCrudManager as Crud;
use Splash\Sylius\Services\ProductImagesManager as Images;
use Splash\Sylius\Services\ProductPricingManager as Pricing;
use Splash\Sylius\Services\ProductTranslationsManager as Translations;
use Sylius\Bundle\CoreBundle\Doctrine\ORM\ProductVariantRepository as Variants;
use Sylius\Component\Product\Model\ProductInterface;
use Sylius\Component\Product\Model\ProductVariantInterface;
use Sylius\Component\Product\Factory\ProductFactory as Factory;

/**
 * Sylius Product Object
 */
class Product extends AbstractStandaloneObject
{
    // Splash Php Core Traits
    use IntelParserTrait;
    use SimpleFieldsTrait;
    use ListsTrait;
    use GenericFieldsTrait;

    // Product Traits
    use Product\CrudTrait;
    use Product\ObjectsListTrait;
    use Product\CoreTrait;
    use Product\ShippingTrait;
    use Product\DescriptionsTrait;
    use Product\PricingTrait;
    use Product\StocksTrait;
    use Product\ImagesTrait;

    // Products Variants Traits
    use Product\Variants\CoreTrait;
    use Product\Variants\AttributesTrait;

    //====================================================================//
    // Object Definition Parameters
    //====================================================================//

    /**
     *  Object Disable Flag. Uncomment thius line to Override this flag and disable Object.
     */
//    protected static    $DISABLED        =  True;

    /**
     *  Object Name (Translated by Module)
     */
    protected static $NAME = "Product";

    /**
     *  Object Description (Translated by Module).
     */
    protected static $DESCRIPTION = 'Sylius Product Object';

    /**
     *  Object Icon (FontAwesome or Glyph ico tag).
     */
    protected static $ICO = 'fa fa-product-hunt';

    //====================================================================//
    // Private variables
    //====================================================================//

    /**
     * @var ProductVariantInterface
     */
    protected $object;

    /**
     * @var ProductInterface
     */
    protected $product;

    /**
     * @var Crud
     */
    protected $crud;

    /**
     * @var Translations
     */
    protected $translations;

    /**
     * @var Images
     */
    protected $images;

    /**
     * @var Pricing
     */
    protected $pricing;

    /**
     * @var Attributes
     */
    protected $attributes;

    /**
     * @var Factory
     */
    protected $factory;

    //====================================================================//
    // Service Constructor
    //====================================================================//

    /**
     * Service Constructor
     *
     * @param TranslatorInterface    $translator
     * @param EntityManagerInterface $entityManager
     * @param CustomerRepository     $repository
     * @param Factory                $factory
     */
    public function __construct(Variants $variants, Crud $crudService, Translations $translations, Images $images, Pricing $pricing, Attributes $attributes)
    {
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

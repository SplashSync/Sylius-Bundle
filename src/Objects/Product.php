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

use Doctrine\ORM\EntityManagerInterface as Doctrine;
use Splash\Bundle\Models\AbstractStandaloneObject;
use Splash\Client\Splash;
use Splash\Models\Objects\IntelParserTrait;
use Splash\Models\Objects\ListsTrait;
use Splash\Models\Objects\SimpleFieldsTrait;
use Splash\Models\Objects\GenericFieldsTrait;
use Sylius\Bundle\CoreBundle\Doctrine\ORM\ProductRepository as PrRepository;
use Sylius\Bundle\CoreBundle\Doctrine\ORM\ProductVariantRepository as PrVaRepository;
use Sylius\Bundle\ChannelBundle\Doctrine\ORM\ChannelRepository;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Product\Factory\ProductFactory as Factory;
use Symfony\Component\Translation\TranslatorInterface as Translator;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;

//use Splash\Sylius\Objects\Traits\ProductSlugTrait;
use Splash\Sylius\Helpers\ChannelsAwareTrait;
use Splash\Sylius\Helpers\ProdutsAwareTrait;
use Splash\Sylius\Services\ProductTranslationsManager as Translations;
use Splash\Sylius\Services\ProductImagesManager as Images;
use Splash\Sylius\Services\ProductPricingManager as Pricing;

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

    // Sylius Main Helpers Traits
    // 
//    use ChannelsAwareTrait;
    
    // Product Traits
    use Product\CrudTrait;
    use Product\ObjectsListTrait;
    use Product\CoreTrait;
    use Product\ShippingTrait;
    use Product\DescriptionsTrait;
//    use Product\PricingTrait;
    use Product\ImagesTrait;

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
    public function __construct(Doctrine $entityManager, PrRepository $products, PrVaRepository $variants, Translations $translations, Images $images, Pricing $pricing)
    {
        //====================================================================//
        // Link to Doctrine Entity Manager Services
        $this->entityManager = $entityManager;
        //====================================================================//§:─ ,nbvcx)àç&  
        //.
        // Link to Product Variants Repository
        $this->repository = $variants;
        //====================================================================//
        // Link to Splash Sylius Translations Manager
        $this->translations = $translations;
        //====================================================================//
        // Link to Splash Sylius Images Manager
        $this->images = $images;
        //====================================================================//
        // Link to Splash Sylius Channel Pricing Manager
        $this->pricing = $pricing;
    }
}

//
///**
// * @abstract    Description of Customer
// *
// * @author B. Paquier <contact@splashsync.com>
// * @SPL\Object( type                    =   "Product",
// *              name                    =   "Product",
// *              description             =   "Sylius Product Object",
// *              icon                    =   "fa fa-product-hunt",
// *              enable_push_created     =    false,
// *              target                  =   "Sylius\Component\Core\Model\ProductVariant",
// *              repository_service      =   "sylius.repository.product_variant",
// *              transformer_service     =   "Splash.Sylius.Products.Transformer"
// * )
// *
// */
//class Product
//{
//    use ProductSlugTrait;
//    
//    

//    
//    //====================================================================//
//    // PRODUCT STOCKS
//    //====================================================================//
//    
//    /**
//     * @SPL\Field(
//     *          id      =   "onHand",
//     *          type    =   "int",
//     *          name    =   "On Hand (Stock)",
//     *          itemtype=   "http://schema.org/Offer", itemprop="inventoryLevel",
//     * )
//     */
//    protected $onHand;
//            
//    /**
//     * @SPL\Field(
//     *          id      =   "outofstock",
//     *          type    =   "bool",
//     *          name    =   "Out of Stock",
//     *          itemtype=   "http://schema.org/ItemAvailability", itemprop="OutOfStock",
//     *          write   =   false,
//     * )
//     */
//    protected $outOfStock;

//}

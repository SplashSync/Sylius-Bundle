<?php

namespace Splash\Sylius\Objects;

use Splash\Bundle\Annotation as SPL;

use Splash\Sylius\Objects\Traits\ProductSlugTrait;



/**
 * @abstract    Description of Customer
 *
 * @author B. Paquier <contact@splashsync.com>
 * @SPL\Object( type                    =   "Product",
 *              name                    =   "Product",
 *              description             =   "Sylius Product Object",
 *              icon                    =   "fa fa-product-hunt",
 *              enable_push_created     =    false,
 *              target                  =   "Sylius\Component\Core\Model\ProductVariant",
 *              repository_service      =   "sylius.repository.product_variant",
 *              transformer_service     =   "Splash.Sylius.Products.Transformer"
 * )
 * 
 */
class Product {

    use ProductSlugTrait;
    
    //====================================================================//
    // CORE FIELDS
    //====================================================================//
    
    /**
     * @SPL\Field(  
     *          id      =   "productCode",
     *          type    =   "varchar",
     *          name    =   "Reference",
     *          itemtype=   "http://schema.org/Product", itemprop="model",
     *          inlist  =   true,
     *          required=   true,
     * )
     */
    protected $productCode;
    
//    /**
//     * @SPL\Field(  
//     *          id      =   "code",
//     *          type    =   "varchar",
//     *          name    =   "Variant Code",
//     *          itemtype=   "http://schema.org/Product", itemprop="productID",
//     * )
//     */
//    protected $code;
    
    /**
     * @SPL\Field(  
     *          id      =   "enabled",
     *          type    =   "bool",
     *          name    =   "Active",
     *          itemtype=   "http://schema.org/Product", itemprop="offered",
     * )
     */
    protected $enabled;    
    
    //====================================================================//
    // PRODUCT DESCRIPTIONS
    //====================================================================//
    
    /**
     * @SPL\Field(  
     *          id      =   "name",
     *          type    =   "mvarchar",
     *          name    =   "Name",
     *          itemtype=   "http://schema.org/Product", itemprop="name",
     *          group   =   "Translations",
     * )
     */
    protected $name;    
    
    /**
     * @SPL\Field(  
     *          id      =   "shortDescription",
     *          type    =   "mvarchar",
     *          name    =   "Short Description",
     *          itemtype=   "http://schema.org/Product", itemprop="description",
     *          group   =   "Translations",
     *          asso    =   { "name" },
     * )
     */
    protected $shortDescription;        
    
    /**
     * @SPL\Field(  
     *          id      =   "description",
     *          type    =   "mtext",
     *          name    =   "Long Description",
     *          itemtype=   "http://schema.org/Article", itemprop="articleBody",
     *          group   =   "Translations",
     *          asso    =   { "name" },
     * )
     */
    protected $description;   
    

    
    /**
     * @SPL\Field(  
     *          id      =   "metaDescription",
     *          type    =   "mvarchar",
     *          name    =   "Meta Desription",
     *          itemtype=   "http://schema.org/Article", itemprop="headline",
     *          group   =   "SEO",
     *          asso    =   { "name" },
     * )
     */
    protected $metaDescription;  
    
    //====================================================================//
    // PRODUCT SPECIFICATIONS
    //====================================================================//

    /**
     * @SPL\Field(  
     *          id      =   "weight",
     *          type    =   "double",
     *          name    =   "Weight",
     *          itemtype=   "http://schema.org/Product", itemprop="weight",
     *          group   =   "Specifications",
     * )
     */
    protected $weight;    
   
    /**
     * @SPL\Field(  
     *          id      =   "height",
     *          type    =   "double",
     *          name    =   "Height",
     *          itemtype=   "http://schema.org/Product", itemprop="height",
     *          group   =   "Specifications",
     * )
     */
    protected $height; 
    
    /**
     * @SPL\Field(  
     *          id      =   "width",
     *          type    =   "double",
     *          name    =   "Width",
     *          itemtype=   "http://schema.org/Product", itemprop="width",
     *          group   =   "Specifications",
     * )
     */
    protected $width;     
    
    /**
     * @SPL\Field(  
     *          id      =   "depth",
     *          type    =   "double",
     *          name    =   "Depth",
     *          itemtype=   "http://schema.org/Product", itemprop="depth",
     *          group   =   "Specifications",
     * )
     */
    protected $depth;  
    
    //====================================================================//
    // PRICES INFORMATIONS
    //====================================================================//
            
    /**
     * @SPL\Field(  
     *          id      =   "price",
     *          type    =   "price",
     *          name    =   "Price",
     *          itemtype=   "http://schema.org/Product", itemprop="price",
     * )
     */
    protected $price;  
    
    //====================================================================//
    // PRODUCT STOCKS
    //====================================================================//
    
    /**
     * @SPL\Field(  
     *          id      =   "onHand",
     *          type    =   "int",
     *          name    =   "On Hand (Stock)",
     *          itemtype=   "http://schema.org/Offer", itemprop="inventoryLevel",
     * )
     */
    protected $onHand;  
            
    /**
     * @SPL\Field(  
     *          id      =   "outofstock",
     *          type    =   "bool",
     *          name    =   "Out of Stock",
     *          itemtype=   "http://schema.org/ItemAvailability", itemprop="OutOfStock",
     *          write   =   false,
     * )
     */
    protected $outOfStock;  
    
    //====================================================================//
    // PRODUCT IMAGES
    //====================================================================//

    /**
     * @SPL\Field(  
     *          id      =   "image@images",
     *          type    =   "image@list",
     *          name    =   "Images",
     *          itemtype=   "http://schema.org/Product", itemprop="image",
     * )
     */
    protected $images;  
    
//    /**
//     * @SPL\Field(  
//     *          id      =   "code@images",
//     *          type    =   "varchar@list",
//     *          name    =   "Images Codes",
//     *          itemtype=   "http://schema.org/Product", itemprop="imageName",
//     *          write   =   false,
//     * )
//     */
//    protected $imagesCodes;     
}

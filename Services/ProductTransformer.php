<?php

/*
 * This file is part of SplashSync Project.
 *
 * Copyright (C) Splash Sync <www.splashsync.com>
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * @abstract    Sylius Bundle Data Transformer for Splash Bundle
 * @author      B. Paquier <contact@splashsync.com>
 */

namespace   Splash\Sylius\Services;

use Splash\Local\Objects\Transformer;
use Splash\Models\ObjectBase;
use Splash\Core\SplashCore as Splash;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

use Sylius\Component\Core\Model\ProductImage;
use Sylius\Component\Core\Model\ChannelPricing;


use Splash\Sylius\Objects\Traits\ProductSlugTrait;

/**
 * Description of ProductTransformer
 *
 * @author nanard33
 */
class ProductTransformer extends Transformer {
    
    use ProductSlugTrait;
    
    public function __construct($Translator, $Router, $Factory, $Manager, $ChannelsRepository, $Parameters) {

        //====================================================================//
        // Symfony Translator         
        $this->translator   = $Translator;
        //====================================================================//
        // Symfony Router        
        $this->router       = $Router;
        //====================================================================//
        // Sylius Product Factory         
        $this->factory      = $Factory;
        //====================================================================//
        // Sylius Product Manager         
        $this->manager      = $Manager;
        //====================================================================//
        // Sylius Product Manager         
        $this->channels     = $ChannelsRepository;
        //====================================================================//
        // Sylius Bundle Parameters 
        $this->parameters   = $Parameters;
        
        return;
    }

    //====================================================================//
    // OBJECT CREATE & DELETE
    //====================================================================//
    
    /**
     *  @abstract       Create a New Object
     * 
     *  @param  mixed   $Manager        Local Object Entity/Document Manager
     *  @param  string  $Target         Local Object Class Name
     * 
     *  @return         mixed
     */
    public function create($Manager, $Target) {
        //====================================================================//
        // Create a New Object
        $Product = $this->factory->createWithVariant();       
        //====================================================================//
        // Persist New Object        
        $this->manager->persist($Product);            
        //====================================================================//
        // Return a New Object
        return  $Product->getVariants()->first();
    }

    /**
     *  @abstract       Create a New Object
     * 
     *  @param  mixed   $Manager        Local Object Entity/Document Manager
     *  @param  string  $Object         Local Object
     * 
     *  @return         mixed
     */
    public function delete($Manager, $Object) {
        //====================================================================//
        // Saftey Check
        if ( !$Object ) { 
            return False; 
        }
        //====================================================================//
        // Load Product from Variant
        $Product    =   $Object->getProduct();
        //====================================================================//
        // Delete Product Variant from Product
        $Product->removeVariant($Object);
        //====================================================================//
        // If Product has no more Variant
        if ( $Product->getVariants()->count() == 0 ) {
            //====================================================================//
            // Delete Product
            $this->manager->remove($Product);    
        }
        $this->manager->flush();           
        return True;
    }    
    
    
    //====================================================================//
    // CORE FIELDS
    //====================================================================//
        
    public function getProductCode($Variant){
        
        return $Variant->getCode();
    }
    
    public function setProductCode($Variant, $Data){
        if ( !$Variant->getProduct()->getCode() ) {
            $Variant->getProduct()->setCode($Data);
        } 
        return $Variant->setCode($Data);
    }    
            
    public function getEnabled($Variant)
    {
        return $Variant->getProduct()->isEnabled();
    }
    
    public function setEnabled($Variant, $Data)
    {
        return $Variant->getProduct()->setEnabled($Data);
    }         
    
    //====================================================================//
    // PRICES INFORMATIONS
    //====================================================================//

    public function getDefaultChannelPricing($Variant)
    {
        //====================================================================//
        // Identify Default ChannelPricing
        foreach ($Variant->getChannelPricings() as $ChannelPricing) {
            $Code = method_exists($ChannelPricing,'getChannel') ? $ChannelPricing->getChannel()->getCode() : $ChannelPricing->getChannelCode();
            if ( $Code == $this->parameters["default_channel"]) {
                return $ChannelPricing;
            }            
        }
        //====================================================================//
        // Create Channel Price if Needed
        $ChannelPricing = new ChannelPricing();
        $this->manager->persist($ChannelPricing);
        //====================================================================//
        // Identify Default ChannelPricing in Parameters
        if (method_exists($ChannelPricing,'setChannel')) {
            $Channel = $this->channels->findOneByCode($this->parameters["default_channel"]);
            $ChannelPricing->setChannel($Channel);
        } else {
            $ChannelPricing->setChannelCode($this->parameters["default_channel"]);
        }
        $ChannelPricing->setProductVariant($Variant);
        
        //====================================================================//
        // Add Channel Pricing to Variant
        $Variant->getChannelPricings()->add($ChannelPricing);
        //====================================================================//
        // Return New Channel Pricing
        return $ChannelPricing; 
    }   
    
    public function getPrice($Variant)
    {
//        dump($this->channels->findOneByCode($this->parameters["default_channel"]));
                
        //====================================================================//
        // Identify Default Channel Price
        $ChannelPrice   = $this->getDefaultChannelPricing($Variant);
        //====================================================================//
        // Retreive Price Currency
        if (method_exists($ChannelPrice,'getChannel')) {
            $Currency       =   $ChannelPrice->getChannel()->getBaseCurrency();
        } else {
            $Currency       =   $this->channels->findOneByCode($ChannelPrice->getChannelCode())->getBaseCurrency();
        }
        //====================================================================//
        // TODO : Select Default TaxZone in Parameters
        // Retreive Price TAX Percentile
        if ($Variant->getTaxCategory()) {
            $TaxRate = $Variant->getTaxCategory()->getRates()->first()->getAmount() * 100;
        } else {
            $TaxRate = 0.0;
        }
        
        return ObjectBase::Price_Encode(
                doubleval($ChannelPrice->getPrice() / 100),            // No TAX Price 
                $TaxRate,                                          // TAX Percent
                Null, 
                $Currency->getCode(),
                $Currency->getCode(),
                $Currency->getName());
    }   
    
    public function setPrice($Variant, $Data)
    {
        if ( !isset($Data["ht"]) ) {
            return;
        }
        //====================================================================//
        // Identify Default Channel Price
        $ChannelPrice   = $this->getDefaultChannelPricing($Variant);
        //====================================================================//
        // Update Product Price
        $ChannelPrice->setPrice($Data["ht"] * 100);
        return ;
    }      
    
    //====================================================================//
    // PRODUCT STOCKS
    //====================================================================//
        
    public function getOutOfStock($Variant)
    {
        if ($Variant->isTracked()) {
            return ($Variant->getOnHand() > 0 ) ? False : True;
        } 
        return False;
    }    

    //====================================================================//
    // PRODUCT DESCRIPTIONS
    //====================================================================//
        
    public function getName($Variant)
    {
        return $this->getTranslated($Variant, __FUNCTION__);
    } 
    
    public function setName($Variant, $Data)
    {
        return $this->setTranslated($Variant, $Data, __FUNCTION__);
    } 
    
    public function getShortDescription($Variant)
    {
        return $this->getTranslated($Variant, __FUNCTION__);
    } 
    
    public function setShortDescription($Variant, $Data)
    {
        return $this->setTranslated($Variant, $Data, __FUNCTION__);
    } 
    
    public function getDescription($Variant)
    {
        return $this->getTranslated($Variant, __FUNCTION__);
    } 
    
    public function setDescription($Variant, $Data)
    {
        return $this->setTranslated($Variant, $Data, __FUNCTION__);
    }

    
    public function getMetaDescription($Variant)
    {
        return $this->getTranslated($Variant, __FUNCTION__);
    } 
    
    public function setMetaDescription($Variant, $Data)
    {
        return $this->setTranslated($Variant, $Data, __FUNCTION__);
    }
    
    public function getTranslated($Variant,$Function)
    {
        $Response = array();
        foreach ($Variant->getProduct()->getTranslations() as $LanguageCode => $Translation) {
            $Response[$LanguageCode] = $Translation->$Function();
        }
        return $Response;
    }     
    
    public function setTranslated($Variant, $Data, $Function)
    {
        $Translations   =   $Variant->getProduct()->getTranslations();
        foreach ($Data as $LanguageCode => $Value) {
            if (!isset($Translations[$LanguageCode])) {
                //====================================================================//
                // Add Translation
                $Translation = new \Sylius\Component\Core\Model\ProductTranslation();
                $Translation->setLocale($LanguageCode);
                $Translation->setTranslatable($Variant->getProduct());
                $Translation->setSlug( uniqid($Variant->getCode()) );
                $Translations[$LanguageCode] = $Translation;
            }
            $Translations[$LanguageCode]->$Function($Value);
        }
    }      
    
    //====================================================================//
    // MANAGE UNIQUE PRODUCT SLUGS
    //====================================================================//
    
    

    
    //====================================================================//
    // PRODUCT IMAGES
    //====================================================================//
    
    public function addImages($Variant)
    {
        //====================================================================//
        // Create a New Product Image
        $Image = new \Sylius\Component\Core\Model\ProductImage();
        $this->manager->persist($Image);
        //====================================================================//
        // Setup New Product Image
        $Image->setOwner($Variant->getProduct());
        $ImageCode  = $Variant->getCode() ? $Variant->getCode() : $Variant->getProduct()->getCode();
        $ImageCode .= "-" . uniqid();
        $Image->setCode($ImageCode);
        //====================================================================//
        // Add to Product Images
        $Variant->getProduct()->getImages()->add($Image);
        return $Image;
    }    
    
    public function removeImages($Variant, $Image)
    {
        //====================================================================//
        // Remove From Product Images
        $Variant->getProduct()->getImages()->remove($Image);
        //====================================================================//
        // DeleteProduct Image
        $this->manager->remove($Image);
    }    
    
    public function getImages($Variant)
    {
        return $Variant->getProduct()->getImages();
    }
    
    public function getImage(ProductImage $Image)
    {
        //====================================================================//
        // Generate Images Base Path
        $BasePath   = $this->parameters["images_folder"] . "/";
        //====================================================================//
        // Generate Public Url
        $PublicUrl = $this->router->generate(
                "liip_imagine_filter", 
                array("filter" => "sylius_large", "path" => $Image->getPath()) , 
                UrlGeneratorInterface::ABSOLUTE_URL );
        //====================================================================//
        // Add Image
        return  ObjectBase::Img_Encode(
                        $Image->getCode(), 
                        basename($Image->getPath()), 
                        $BasePath . dirname($Image->getPath()) . "/", 
                        $PublicUrl
                    );
    }     
    
    public function setImage($Image, $Data)
    {
        //====================================================================//
        // Generate Images Base Path
        $BasePath   = realpath($this->parameters["images_folder"]) . "/";
        
        //====================================================================//
        // Check if Image Needs to Be Updated
        //====================================================================//

        //====================================================================//
        // Check if Image is Defined
        if ($Image->getPath()) {
            //====================================================================//
            // Compute Image CheckSum
            $Md5 = md5_file($BasePath . $Image->getPath());
            //====================================================================//
            // Compare Image CheckSum
            if ($Md5 === $Data["md5"]) {
                return True;
            }
            //====================================================================//
            // Delete Outdated Image
            Splash::File()->DeleteFile($BasePath . $Image->getPath(), $Data["md5"]);
        }
        //====================================================================//
        // DownLoad Image from Splash Server
        $NewImageFile    =   Splash::File()->getFile($Data["file"],$Data["md5"]);
        //====================================================================//
        // File Not Imported => Exit
        if ( $NewImageFile == False ) {
            return False;
        }            
        //====================================================================//
        // Generate Image Encoded Path
        do {
            $hash = md5(uniqid(mt_rand(), true));
            $ImagePath = $this->expandPath($hash.'.'.pathinfo($Data["file"], PATHINFO_EXTENSION));
        } while (is_file($BasePath . $ImagePath));
        //====================================================================//
        // Check if folder exists or create it
        if (!is_dir(dirname(dirname($BasePath . $ImagePath)))) {    
            mkdir(dirname(dirname($BasePath . $ImagePath)),0775,TRUE);    
        }
        if (!is_dir(dirname($BasePath . $ImagePath))) {    
            mkdir(dirname($BasePath . $ImagePath),0775,TRUE);    
        }
        //====================================================================//
        // Write Image On Folder
        Splash::File()->WriteFile($BasePath,$ImagePath,$NewImageFile["md5"],$NewImageFile["raw"]);
        //====================================================================//
        // Setup Image Path
        $Image->setPath($ImagePath);
        return True;
    }      
    
    /**
     * @param string $path
     *
     * @return string
     */
    private function expandPath($path)
    {
        return sprintf(
            '%s/%s/%s',
            substr($path, 0, 2),
            substr($path, 2, 2),
            substr($path, 4)
        );
    }    
    
}

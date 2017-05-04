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

use Splash\Sylius\Objects\Traits\OrderItemsTrait;
use Splash\Sylius\Objects\Traits\OrderMetadataTrait;
use Splash\Sylius\Objects\Traits\OrderPricesTrait;

/**
 * Description of OrderTransformer
 *
 * @author nanard33
 */
class OrdersTransformer extends Transformer {
    
    public function __construct($Manager, $ChannelsRepository, $Parameters) {

        //====================================================================//
        // Sylius Order Manager         
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
        // Load Default Channel
        $DefaultChannel    =    $this->channels
                ->findOneByCode($this->parameters["default_channel"]);
        if (!$DefaultChannel) {
            return Null;
        }
        //====================================================================//
        // Create a New Object
        $Order  =   new $Target();
        $Order->setChannel($DefaultChannel);
        $Order->setLocaleCode($DefaultChannel->getDefaultLocale()->getCode());
        $Order->setCurrencyCode($DefaultChannel->getBaseCurrency()->getCode());

        //====================================================================//
        // Persist New Object        
        $Manager->persist($Order); 
        //====================================================================//
        // Return a New Object
        return  $Order;
    }

//    /**
//     *  @abstract       Create a New Object
//     * 
//     *  @param  mixed   $Manager        Local Object Entity/Document Manager
//     *  @param  string  $Object         Local Object
//     * 
//     *  @return         mixed
//     */
//    public function delete($Manager, $Object) {
//        //====================================================================//
//        // Saftey Check
//        if ( !$Object ) { 
//            return False; 
//        }
//        //====================================================================//
//        // Load Product from Variant
//        $Product    =   $Object->getProduct();
//        //====================================================================//
//        // Delete Product Variant from Product
//        $Product->removeVariant($Object);
//        //====================================================================//
//        // If Product has no more Variant
//        if ( $Product->getVariants()->count() == 0 ) {
//            //====================================================================//
//            // Delete Product
//            $this->manager->remove($Product);    
//        }
//        $this->manager->flush();           
//        return True;
//    }    
        
    //====================================================================//
    // STATUS INFORMATIONS
    //====================================================================//

    use OrderMetadataTrait;
    
    //====================================================================//
    // ORDER ITEMS
    //====================================================================//
    
    use OrderItemsTrait;
  
    //====================================================================//
    // PRICES INFORMATIONS
    //====================================================================//
          
    use OrderPricesTrait;
         
}

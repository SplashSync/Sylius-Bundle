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

/**
 * Description of OrderTransformer
 *
 * @author nanard33
 */
class OrdersTransformer extends Transformer {
    

    //====================================================================//
    // PRICES INFORMATIONS
    //====================================================================//

    public function getTotal($Order)
    {
        return doubleval($Order->getTotal() / 100);
    }    

    
//    public function getTotal($Order)
//    {
//        //====================================================================//
//        // Retreive Price Currency
//        $Currency       =   $Order->getChannel()->getBaseCurrency();
//        //====================================================================//
//        // Retreive Price TAX Percentile
////        if ($Order->getTaxCategory()) {
////            $TaxRate = $Order->getTaxCategory()->getRates()->first()->getAmount() * 100;
////        } else {
//            $TaxRate = 0.0;
////        }
//        
//        return ObjectBase::Price_Encode(
//                doubleval($Order->getTotal() / 100),            // No TAX Price 
//                $TaxRate,                                          // TAX Percent
//                Null, 
//                $Currency->getCode(),
//                $Currency->getCode(),
//                $Currency->getName());
//    }    
    
    
}

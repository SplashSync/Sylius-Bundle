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

<?php

namespace Splash\Sylius\Objects;

use Splash\Bundle\Annotation as SPL;

use Splash\Sylius\Objects\Traits\OrderCoreTrait;
use Splash\Sylius\Objects\Traits\OrderItemsTrait;
use Splash\Sylius\Objects\Traits\OrderMetadataTrait;
use Splash\Sylius\Objects\Traits\OrderPaymentsTrait;
use Splash\Sylius\Objects\Traits\OrderPricesTrait;

/**
 * @abstract    Description of Invoice
 *
 * @author B. Paquier <contact@splashsync.com>
 * @SPL\Object( type                    =   "Invoice",
 *              disabled                =   false,
 *              name                    =   "Customer Invoice",
 *              description             =   "Sylius Order Object",
 *              icon                    =   "fa fa-money",
 *              allow_push_created      =   false,
 *              allow_push_updated      =   false,
 *              allow_push_deleted      =   false,
 *              enable_push_created     =   false,
 *              enable_push_deleted     =   false,
 *              target                  =   "Sylius\Component\Core\Model\Order",
 *              transformer_service     =   "Splash.Sylius.Orders.Transformer"
 * )
 *
 */
class Invoice
{
    
    //====================================================================//
    // CORE INFORMATIONS
    //====================================================================//

    use OrderCoreTrait;
    
    //====================================================================//
    // ITEMS INFORMATIONS
    //====================================================================//
    
    use OrderItemsTrait;
    
    //====================================================================//
    // STATUS INFORMATIONS
    //====================================================================//
    
    use OrderMetadataTrait;

    //====================================================================//
    // PRICES INFORMATIONS
    //====================================================================//
          
    use OrderPricesTrait;
    use OrderPaymentsTrait;
}

<?php

namespace Splash\Sylius\Objects\Traits;

use Splash\Bundle\Annotation as SPL;

trait OrderStatusTrait {

    /**
     * @SPL\Field(  
     *          id      =   "Status",
     *          type    =   "varchar",
     *          name    =   "Status",
     *          itemtype=   "http://schema.org/Order", itemprop="orderStatus",
     *          write   =   false,
     *          group   =   "Meta",
     * )
     */
    protected $status; 
    
    public function getStatus($Order)
    {
        if ( $this->getIsDraft($Order) ) {
            return "OrderDraft";
        }
        
        if ( $this->getIsValidated($Order) && $this->getIsShipped($Order) && $this->getIsPaid($Order) ) {
            return "OrderDelivered";
        }
        
        if ( $this->getIsValidated($Order) && $this->getIsPaid($Order) ) {
            return "OrderInTransit";
        }
        
        if ( $this->getIsValidated($Order) ) {
            return "OrderProcessing";
        }
        
        return "Unknown";        
    }    
    
    
}

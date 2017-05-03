<?php

namespace Splash\Sylius\Objects\Traits;

use Splash\Bundle\Annotation as SPL;

use Sylius\Component\Core\OrderCheckoutStates;
use Sylius\Component\Core\OrderPaymentStates;
use Sylius\Component\Core\OrderShippingStates;

trait OrderMetadataTrait {

    /**
     * @SPL\Field(  
     *          id      =   "isDraft",
     *          type    =   "bool",
     *          name    =   "Draft",
     *          itemtype=   "http://schema.org/OrderStatus", itemprop="OrderDraft",
     *          write   =   false,
     *          group   =   "Meta",
     * )
     */
    protected $isDraft; 
    
    public function getIsDraft($Order)
    {
        return !$this->getIsValidated($Order);
    }    
    
    /**
     * @SPL\Field(  
     *          id      =   "isValidated",
     *          type    =   "bool",
     *          name    =   "Checkout Completed",
     *          itemtype=   "http://schema.org/OrderStatus", itemprop="OrderProcessing",
     *          write   =   false,
     *          group   =   "Meta",
     * )
     */
    protected $isValidated; 
    
    public function getIsValidated($Order)
    {
        return ($Order->getCheckoutState() === OrderCheckoutStates::STATE_COMPLETED ) ? True : False;
    }    
    
    /**
     * @SPL\Field(  
     *          id      =   "isShipped",
     *          type    =   "bool",
     *          name    =   "Shipping Completed",
     *          itemtype=   "http://schema.org/OrderStatus", itemprop="OrderDelivered",
     *          write   =   false,
     *          group   =   "Meta",
     * )
     */
    protected $isShipped;   
    
    public function getIsShipped($Order)
    {
        if ( in_array($Order->getShippingState(), [OrderShippingStates::STATE_PARTIALLY_SHIPPED, OrderShippingStates::STATE_SHIPPED]) ) {
            return True;
        }
        return False;
    }    
     
    /**
     * @SPL\Field(  
     *          id      =   "isPaid",
     *          type    =   "bool",
     *          name    =   "Payment Completed",
     *          itemtype=   "http://schema.org/OrderStatus", itemprop="OrderPaid",
     *          write   =   false,
     *          group   =   "Meta",
     * )
     */
    protected $isPaid;
   
    public function getIsPaid($Order)
    {
        if ( in_array($Order->getPaymentState(), [OrderPaymentStates::STATE_PAID, OrderPaymentStates::STATE_PARTIALLY_REFUNDED, OrderPaymentStates::STATE_REFUNDED]) ) {
            return True;
        }
        return False;
    }        
    
}

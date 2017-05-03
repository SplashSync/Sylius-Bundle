<?php

namespace Splash\Sylius\Objects\Traits;

use Splash\Bundle\Annotation as SPL;

trait OrderCoreTrait {
    
    /**
     * @SPL\Field(  
     *          id      =   "customer",
     *          type    =   "objectid::ThirdParty",
     *          name    =   "Customer",
     *          itemtype=   "http://schema.org/Organization", itemprop="ID",
     *          inlist  =   false,
     *          required=   true,
     * )
     */
    protected $customer;
        
    /**
     * @SPL\Field(  
     *          id      =   "number",
     *          type    =   "varchar",
     *          name    =   "Order Reference",
     *          itemtype=   "http://schema.org/Order", itemprop="orderNumber",
     *          inlist  =   true,
     *          required=   true,
     * )
     */
    protected $number;
    
    
    /**
     * @SPL\Field(  
     *          id      =   "checkoutCompletedAt",
     *          type    =   "date",
     *          name    =   "Order Date",
     *          itemtype=   "http://schema.org/Order", itemprop="orderDate",
     *          inlist  =   true,
     *          required=   true,
     * )
     */
    protected $checkoutCompletedAt;
    
    //====================================================================//
    // ADDRESS
    //====================================================================//

    
    /**
     * @SPL\Field(  
     *          id      =   "shippingAddress",
     *          type    =   "objectid::Address",
     *          name    =   "Shipping Address",
     *          itemtype=   "http://schema.org/Organization", itemprop="ID",
     *          inlist  =   false,
     *          required=   true,
     * )
     */
    protected $shippingAddress;

    /**
     * @SPL\Field(  
     *          id      =   "billingAddress",
     *          type    =   "objectid::Address",
     *          name    =   "Billing Address",
     *          itemtype=   "http://schema.org/Organization", itemprop="ID",
     *          inlist  =   false,
     *          required=   true,
     * )
     */
    protected $billingAddress;    
    
    //====================================================================//
    // OTHER INFORMATIONS
    //====================================================================//
              
    /**
     * @SPL\Field(  
     *          id      =   "notes",
     *          type    =   "text",
     *          name    =   "Note",
     *          itemtype=   "http://schema.org/Order", itemprop="description",
     * )
     */
    protected $notes; 
    
}

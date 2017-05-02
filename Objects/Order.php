<?php

namespace Splash\Sylius\Objects;

use Splash\Bundle\Annotation as SPL;

/**
 * @abstract    Description of Invoice
 *
 * @author B. Paquier <contact@splashsync.com>
 * @SPL\Object( type                    =   "Order",
 *              disabled                =   false,
 *              name                    =   "Sylius Order",
 *              description             =   "Sylius Order Object",
 *              icon                    =   "fa fa-cart",
 *              allow_push_created      =   false,
 *              enable_push_created     =   false,
 *              target                  =   "Sylius\Component\Core\Model\Order",
 *              transformer_service     =   "Splash.Sylius.Orders.Transformer"
 * )
 * 
 */
class Order {

    //====================================================================//
    // CORE INFORMATIONS
    //====================================================================//

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
    // CORE INFORMATIONS
    //====================================================================//

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
    // PRICES INFORMATIONS
    //====================================================================//
          
    /**
     * @SPL\Field(  
     *          id      =   "total",
     *          type    =   "double",
     *          name    =   "Total",
     *          itemtype=   "http://schema.org/Invoice", itemprop="totalPaymentDueTaxIncluded",
     *          inlist  =   true,
     *          write   =   false,
     * )
     */
    protected $total;      
    
//
//    /**
//     * @SPL\Field(  
//     *          id      =   "firstName",
//     *          type    =   "varchar",
//     *          name    =   "First Name",
//     *          itemtype=   "http://schema.org/Person", itemprop="familyName",
//     *          inlist  =   true,
//     * )
//     */
//    protected $firstName;
//    
//    /**
//     * @SPL\Field(  
//     *          id      =   "lastName",
//     *          type    =   "varchar",
//     *          name    =   "Last Name",
//     *          itemtype=   "http://schema.org/Person", itemprop="givenName",
//     * )
//     */
//    protected $lastName;
//    
//    /**
//     * @SPL\Field(  
//     *          id      =   "gender",
//     *          type    =   "varchar",
//     *          name    =   "Social Title",
//     *          itemtype=   "http://schema.org/Person", itemprop="honorificPrefix",
//     *          write   =   false,
//     *          choices =   { "u" : "Unknown", "m" : "Male" , "f" : "Femele" },
//     * )
//     */
//    protected $gender;
//
//    /**
//     * @SPL\Field(  
//     *          id      =   "genderType",
//     *          type    =   "int",
//     *          name    =   "Social Title (ID)",
//     *          itemtype=   "http://schema.org/Person", itemprop="gender",
//     *          notest  =   true,
//     *          choices =   { "2" : "Unknown", "0" : "Male" , "1" : "Femele" },
//     * )
//     */
//    protected $genderType;
//    
//    /**
//     * @SPL\Field(  
//     *          id      =   "birthday",
//     *          type    =   "date",
//     *          name    =   "Birthday",
//     *          itemtype=   "http://schema.org/Person", itemprop="birthDate",
//     * )
//     */
//    protected $birthday;
//
//    /**
//     * @SPL\Field(  
//     *          id      =   "phoneNumber",
//     *          type    =   "phone",
//     *          name    =   "Phone Number",
//     *          itemtype=   "http://schema.org/PostalAddress", itemprop="telephone",
//     * )
//     */
//    protected $phoneNumber;
//    
//    
//    /**
//     * @SPL\Field(  
//     *          id      =   "subscribedToNewsletter",
//     *          type    =   "bool",
//     *          name    =   "Newletter",
//     *          itemtype=   "http://schema.org/Organization", itemprop="newsletter",
//     * )
//     */
//    protected $subscribedToNewsletter = false;
    
    
}

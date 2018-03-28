<?php

namespace Splash\Sylius\Objects;

use Splash\Bundle\Annotation as SPL;

/**
 * @abstract    Description of Customer
 *
 * @author B. Paquier <contact@splashsync.com>
 * @SPL\Object( type                    =   "ThirdParty",
 *              disabled                =   false,
 *              name                    =   "Customer",
 *              description             =   "Sylius Customer Object",
 *              icon                    =   "fa fa-user",
 *              enable_push_created     =    false,
 *              target                  =   "Sylius\Component\Core\Model\Customer",
 *              transformer_service     =   "Splash.Sylius.Transformer"
 * )
 *
 */
class Customer
{
    
    /**
     * @SPL\Field(
     *          id      =   "UserId",
     *          type    =   "varchar",
     *          name    =   "User Id",
     *          itemtype=   "http://schema.org/Organization", itemprop="legalName",
     *          write   =   false,
     * )
     */
    protected $user_id;

    
    /**
     * @SPL\Field(
     *          id      =   "email",
     *          type    =   "email",
     *          name    =   "Email",
     *          itemtype=   "http://schema.org/ContactPoint", itemprop="email",
     *          inlist  =   true,
     *          required=   true,
     * )
     */
    protected $email;

    /**
     * @SPL\Field(
     *          id      =   "firstName",
     *          type    =   "varchar",
     *          name    =   "First Name",
     *          itemtype=   "http://schema.org/Person", itemprop="familyName",
     *          inlist  =   true,
     * )
     */
    protected $firstName;
    
    /**
     * @SPL\Field(
     *          id      =   "lastName",
     *          type    =   "varchar",
     *          name    =   "Last Name",
     *          itemtype=   "http://schema.org/Person", itemprop="givenName",
     * )
     */
    protected $lastName;
    
    /**
     * @SPL\Field(
     *          id      =   "gender",
     *          type    =   "varchar",
     *          name    =   "Social Title",
     *          itemtype=   "http://schema.org/Person", itemprop="honorificPrefix",
     *          write   =   false,
     *          choices =   { "u" : "Unknown", "m" : "Male" , "f" : "Femele" },
     * )
     */
    protected $gender;

    /**
     * @SPL\Field(
     *          id      =   "genderType",
     *          type    =   "int",
     *          name    =   "Social Title (ID)",
     *          itemtype=   "http://schema.org/Person", itemprop="gender",
     *          notest  =   true,
     *          choices =   { "2" : "Unknown", "0" : "Male" , "1" : "Femele" },
     * )
     */
    protected $genderType;
    
    /**
     * @SPL\Field(
     *          id      =   "birthday",
     *          type    =   "date",
     *          name    =   "Birthday",
     *          itemtype=   "http://schema.org/Person", itemprop="birthDate",
     * )
     */
    protected $birthday;

    /**
     * @SPL\Field(
     *          id      =   "phoneNumber",
     *          type    =   "phone",
     *          name    =   "Phone Number",
     *          itemtype=   "http://schema.org/PostalAddress", itemprop="telephone",
     * )
     */
    protected $phoneNumber;
    
    
    /**
     * @SPL\Field(
     *          id      =   "subscribedToNewsletter",
     *          type    =   "bool",
     *          name    =   "Newletter",
     *          itemtype=   "http://schema.org/Organization", itemprop="newsletter",
     * )
     */
    protected $subscribedToNewsletter = false;
}

<?php

namespace AppBundle\Entity;

use Sylius\Component\Core\Model\Address as SyliusAddress;

use Doctrine\ORM\Mapping as ORM;
use Splash\Bundle\Annotation as SPL;

/**
 * @abstract    Description of Address
 *
 * @author B. Paquier <contact@splashsync.com>
 * @ORM\Entity()
 * @ORM\Table(name="sylius_address")
 * @SPL\Object( type            =   "Address",
 *              disabled        =   false,
 *              name            =   "Sylius Address",
 *              description     =   "Sylius Address Object",
 *              icon            =   "fa fa-enveloppe",
 *              enable_push_created=    false,
 *              realClass       =   "Sylius\Component\Core\Model\Address"
 * )
 * 
 */
class Address extends SyliusAddress {
    
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
     *          id      =   "firstName",
     *          type    =   "varchar",
     *          name    =   "First Name",
     *          itemtype=   "http://schema.org/Person", itemprop="familyName",
     *          inlist  =   true,
     *          required=   true,
     * )
     */
    protected $firstName;
    
    /**
     * @SPL\Field(  
     *          id      =   "lastName",
     *          type    =   "varchar",
     *          name    =   "Last Name",
     *          itemtype=   "http://schema.org/Person", itemprop="givenName",
     *          required=   true,
     * )
     */
    protected $lastName;

    /**
     * @SPL\Field(  
     *          id      =   "phone",
     *          type    =   "phone",
     *          name    =   "Phone Number",
     *          itemtype=   "http://schema.org/PostalAddress", itemprop="telephone",
     * )
     */
    protected $phoneNumber;

    /**
     * @SPL\Field(  
     *          id      =   "street",
     *          type    =   "varchar",
     *          name    =   "Street",
     *          itemtype=   "http://schema.org/PostalAddress", itemprop="streetAddress",
     *          required=   true,
     * )
     */
    protected $street;

    /**
     * @SPL\Field(  
     *          id      =   "company",
     *          type    =   "varchar",
     *          name    =   "Company Name",
     *          itemtype=   "http://schema.org/Organization", itemprop="legalName",
     * )
     */
    protected $company;

    /**
     * @SPL\Field(  
     *          id      =   "city",
     *          type    =   "varchar",
     *          name    =   "City Name",
     *          itemtype=   "http://schema.org/PostalAddress", itemprop="addressLocality",
     *          required=   true,
     * )
     */
    protected $city;
    
    /**
     * @SPL\Field(  
     *          id      =   "postcode",
     *          type    =   "varchar",
     *          name    =   "Zip/Postal Code",
     *          itemtype=   "http://schema.org/PostalAddress", itemprop="postalCode",
     *          required=   true,
     * )
     */
    protected $postcode;

    /**
     * @SPL\Field(  
     *          id      =   "countrycode",
     *          type    =   "country",
     *          name    =   "Country Code",
     *          itemtype=   "http://schema.org/PostalAddress", itemprop="addressCountry",
     *          required=   true,
     * )
     */
    protected $countrycode;
    
//        <field name="provinceCode" column="province_code" type="string" nullable="true" />
//        <field name="provinceName" column="province_name" type="string" nullable="true" />
    
    
}

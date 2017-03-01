<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace AppBundle\Entity;

use Sylius\Component\Core\Model\Customer as SyliusCustomer;
use Sylius\Component\Customer\Model\CustomerInterface;

use Doctrine\ORM\Mapping as ORM;
use Splash\Bundle\Annotation as SPL;

/**
 * @abstract    Description of Customer
 *
 * @author B. Paquier <contact@splashsync.com>
 * @ORM\Entity()
 * @ORM\Table(name="sylius_customer")
 * @SPL\Object( type            =   "ThirdParty",
 *              disabled        =   false,
 *              name            =   "Sylius Customer",
 *              description     =   "Sylius Customer Object",
 *              icon            =   "fa fa-user",
 *              enable_push_created=    false,
 *              realClass       =   "Sylius\Component\Core\Model\Customer"
 * )
 * 
 */
class Customer extends SyliusCustomer {
    
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
    
    public function setEmail($email)
    {
        $this->email            = $email;
        $this->emailCanonical   = strtolower($email);
    }

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
     *          read    =   true,
     *          notest  =   true,
     *          choices =   { "u" : "Unknown", "m" : "Male" , "f" : "Femele" },
     * )
     */
    protected $gender  = CustomerInterface::UNKNOWN_GENDER;

    /**
     * @SPL\Field(  
     *          id      =   "gender_type",
     *          type    =   "int",
     *          name    =   "Social Title (ID)",
     *          itemtype=   "http://schema.org/Person", itemprop="gender",
     *          notest  =   true,
     *          choices =   { "2" : "Unknown", "0" : "Male" , "1" : "Femele" },
     * )
     */
    protected $genderType;
    
    /**
     * @abstract Convert Splash Standard Gender Type to Sylius Type
     */
    public function getGenderType()
    {
        switch ($this->gender) 
        {
            case CustomerInterface::MALE_GENDER:
                return 0;
            case CustomerInterface::FEMALE_GENDER:
                return 1;
            default:    
            case CustomerInterface::UNKNOWN_GENDER:
                return 2;
        }
    }

    /**
     * @abstract Convert Sylius Gender Type to Splash Standard Type
     */
    public function setGenderType($gender)
    {
        switch ($gender) 
        {
            case 0:
                $this->gender   =   CustomerInterface::MALE_GENDER;
                break;
            case 1:
                $this->gender   =   CustomerInterface::FEMALE_GENDER;
                break;
            default:    
            case 2:
                $this->gender   =   CustomerInterface::FEMALE_GENDER;
                break;
        }
    }    

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
     *          id      =   "phone",
     *          type    =   "phone",
     *          name    =   "Phone Number",
     *          itemtype=   "http://schema.org/PostalAddress", itemprop="telephone",
     * )
     */
    protected $phoneNumber;
    
    
    /**
     * @SPL\Field(  
     *          id      =   "newsletter",
     *          type    =   "bool",
     *          name    =   "Newletter",
     *          itemtype=   "http://schema.org/Organization", itemprop="newsletter",
     * )
     */
    protected $subscribedToNewsletter = false;
    
    public function getSubscribedToNewsletter()
    {
        $this->isSubscribedToNewsletter();
    }    


    
}

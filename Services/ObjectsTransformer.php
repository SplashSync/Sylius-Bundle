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

use Sylius\Component\Customer\Model\CustomerInterface;

/**
 * Description of ObjectTransformer
 *
 * @author nanard33
 */
class ObjectsTransformer extends Transformer {
    
    public function __construct($Translator) {
        
        $this->translator = $Translator;
        
        return;
    }

    /**
     * @abstract Create Virtual UserName used As Customer Company NAme
     */
    public function getUserId($Customer)
    {
        return "Sylius" . $Customer->getId();
    }
    
    /**
     * @abstract Override Sylius Customer Setter. Also Set Canonical Email
     */
    public function setEmail($Customer, $email)
    {
        $Customer->setEmail($email);
        $Customer->setEmailCanonical(strtolower($email));
    }
    
    /**
     * @abstract Override Sylius Getter for Translations
     */
    public function getGender($Customer)
    {
        switch ($Customer->getGender()) 
        {
            case CustomerInterface::MALE_GENDER:
                return $this->translator->Trans("sylius.gender.male",[],"messages");
            case CustomerInterface::FEMALE_GENDER:
                return $this->translator->Trans("sylius.gender.female",[],"messages");
            case CustomerInterface::UNKNOWN_GENDER:
                return $this->translator->Trans("sylius.gender.unknown",[],"messages");
            default:    
                return $this->translator->Trans("sylius.gender.unknown",[],"messages");
        }
    }
    
    /**
     * @abstract Convert Splash Standard Gender Type to Sylius Customer Gender Type
     */
    public function getGenderType($Customer)
    {
        switch ($Customer->getGender()) 
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
     * @abstract Convert Sylius Customer Gender Type to Splash Standard Gender Type
     */
    public function setGenderType($Customer, $gender)
    {
        switch ($gender) 
        {
            case 0:
                $Customer->setGender(CustomerInterface::MALE_GENDER);
                break;
            case 1:
                $Customer->setGender(CustomerInterface::FEMALE_GENDER);
                break;
            default:    
            case 2:
                $Customer->setGender(CustomerInterface::UNKNOWN_GENDER);
                break;
        }
    } 
    
    public function getSubscribedToNewsletter($Customer)
    {
        return $Customer->isSubscribedToNewsletter();
    }     

    /**
     * @abstract Format Customer Address Province Code
     */
    public function getProvinceCode($Address)
    {
        if (null === $Address->getCountryCode()) {
            return $Address->getProvinceCode();
        }
        
        return substr($Address->getProvinceCode(), strlen($Address->getCountryCode()) + 1 );
    }
    
}

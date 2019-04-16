<?php

/*
 *  This file is part of SplashSync Project.
 *
 *  Copyright (C) 2015-2019 Splash Sync  <www.splashsync.com>
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Splash\Sylius\Objects\Address;

use Splash\Client\Splash;
use Sylius\Component\Core\Model\AddressInterface;

/**
 * Sylius Address CRUD
 */
trait CrudTrait
{
    use \Splash\Bundle\Helpers\Doctrine\CrudHelperTrait;
    
    private static $requiredFields = array(
        "firstname",
        "lastname",
        "street",
        "city",
        "postcode",
        "countrycode",
    );
        
    /**
     * Create Request Object
     *
     * @return AddressInterface|false
     */
    public function create()
    {
        //====================================================================//
        // Stack Trace
        Splash::log()->trace();

        //====================================================================//
        // Check Customer Id is given
        if (empty($this->in["customer"])) {
            return Splash::log()->err("ErrLocalFieldMissing", __CLASS__, __FUNCTION__, "customer");
        }
        //====================================================================//
        // Load Customer
        $customer = $this->customers->find((int) self::objects()->id($this->in["customer"]));
        if (empty($customer)) {
            return Splash::log()->errTrace("Address : Unable Load Parent Customer");
        }
        //====================================================================//
        // Create New Entity
        $this->object = $this->factory->createNew();
        //====================================================================//
        // Setup required Fields
        foreach (static::$requiredFields as $fieldName) {
            //====================================================================//
            // Check Field is Not Empty
            if (empty($this->in[$fieldName])) {
                return Splash::log()->err("ErrLocalFieldMissing", __CLASS__, __FUNCTION__, $fieldName);
            }
            //====================================================================//
            // Pre-Setup Field Field is Not Empty
            $this->setGeneric($fieldName, $this->in[$fieldName]);
        }
        //====================================================================//
        // Persist New Entity
        $this->repository->add($this->object);

        return $this->object;
    }

}
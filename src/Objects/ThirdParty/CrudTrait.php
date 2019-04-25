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

namespace Splash\Sylius\Objects\ThirdParty;

use Splash\Client\Splash;
use Sylius\Component\Core\Model\CustomerInterface;


/**
 * Sylius Customers CRUD
 */
trait CrudTrait
{
    use \Splash\Sylius\Helpers\Doctrine\CrudTrait;
    
    /**
     * Create Request Object
     *
     * @return CustomerInterface|false
     */
    public function create()
    {
        //====================================================================//
        // Stack Trace
        Splash::log()->trace();

        //====================================================================//
        // Check Customer Email is given
        if (empty($this->in["email"])) {
            return Splash::log()->err("ErrLocalFieldMissing", __CLASS__, __FUNCTION__, "email");
        }
        
        //====================================================================//
        // Create New Entity
        /** @var CustomerInterface $customer */
        $customer = $this->factory->createNew();
        $customer->setEmail($this->in["email"]);
        $this->repository->add($customer);

        return $customer;
    }

}
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

namespace Splash\Sylius\Helpers;

use Sylius\Bundle\CoreBundle\Doctrine\ORM\CustomerRepository;
use Sylius\Component\Core\Model\CustomerInterface;

/**
 * Give Access to Customers for Objects
 */
trait CustomersAwareTrait
{
    /**
     * @var CustomerRepository
     */
    private $customers;

    /**
     * Setup Customers Repository
     *
     * @param CustomerRepository $customers
     * 
     * @return $this
     */
    protected function setCustomersRepository(CustomerRepository $customers): self
    {
        //====================================================================//
        // Store link to Customers Repository
        $this->customers = $customers;
        
        return $this;
    }
    
    /**
     * Get Customers from Splash Field Data
     *
     * @param string $fieldData Object Identifier String.
     * 
     * @return null|CustomerInterface
     */
    public function getCustomer(string $fieldData): ?CustomerInterface
    {
        $customer = $this->customers->find((int) self::objects()->id($fieldData));
                
        return $customer ? $customer : null;
    }
}

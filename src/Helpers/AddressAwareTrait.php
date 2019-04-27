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

use Sylius\Bundle\CoreBundle\Doctrine\ORM\AddressRepository;
use Sylius\Component\Core\Model\AddressInterface;

/**
 * Give Access to Customers Address for Objects
 */
trait AddressAwareTrait
{
    /**
     * @var AddressRepository
     */
    private $addresses;

    /**
     * Get Customers Address from Splash Field Data
     *
     * @param string $fieldData Object Identifier String.
     *
     * @return null|AddressInterface
     */
    public function getAddress(string $fieldData): ?AddressInterface
    {
        $address = $this->addresses->find((int) self::objects()->id($fieldData));

        return $address ? $address : null;
    }

    /**
     * Setup Address Repository
     *
     * @param AddressRepository $addresses
     *
     * @return $this
     */
    protected function setAddressRepository(AddressRepository $addresses): self
    {
        //====================================================================//
        // Store link to Customers Repository
        $this->addresses = $addresses;

        return $this;
    }
}

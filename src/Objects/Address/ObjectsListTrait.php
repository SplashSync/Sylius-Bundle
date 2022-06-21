<?php

/*
 *  This file is part of SplashSync Project.
 *
 *  Copyright (C) Splash Sync  <www.splashsync.com>
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Splash\SyliusSplashPlugin\Objects\Address;

use Sylius\Component\Core\Model\AddressInterface;

/**
 * Sylius Address Objects Lists
 */
trait ObjectsListTrait
{
    use \Splash\Bundle\Helpers\Doctrine\ObjectsListHelperTrait;

    /**
     * Transform Address To List Array Data
     *
     * @param AddressInterface $address
     *
     * @return array
     */
    protected function getObjectListArray(AddressInterface $address): array
    {
        return array(
            'id' => $address->getId(),
            'firstname' => $address->getFirstName(),
            'lastname' => $address->getLastName(),
            'phoneNumber' => $address->getPhoneNumber(),
            'city' => $address->getCity(),
        );
    }
}

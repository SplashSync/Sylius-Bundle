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
use Splash\Sylius\Models\ObjectListHelperTrait;
use Sylius\Component\Core\Model\CustomerInterface;

/**
 * Sylius Customers Objects Lists
 */
trait ObjectsListTrait
{
    use ObjectListHelperTrait;

    /**
     * Transform Curtsomer To List Array Data
     *
     * @param CustomerInterface $customer
     *
     * @return array
     */
    protected function getObjectListArray(CustomerInterface $customer): array
    {
        return array(
            'id' => $customer->getId(),
            'firstname' => $customer->getFirstName(),
            'lastname' => $customer->getLastName(),
            'email' => $customer->getEmail(),
            'phoneNumber' => $customer->getPhoneNumber(),
        );
    }
}

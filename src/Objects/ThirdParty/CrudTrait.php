<?php

/*
 *  Copyright (C) BadPixxel <www.badpixxel.com>
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Splash\SyliusSplashPlugin\Objects\ThirdParty;

use Splash\Client\Splash;
use Sylius\Component\Core\Model\CustomerInterface;

/**
 * Sylius Customers CRUD
 */
trait CrudTrait
{
    use \Splash\SyliusSplashPlugin\Helpers\Doctrine\CrudTrait;

    /**
     * Create Request Object
     *
     * @return null|CustomerInterface
     */
    public function create(): ?CustomerInterface
    {
        //====================================================================//
        // Stack Trace
        Splash::log()->trace();

        //====================================================================//
        // Check Customer Email is given
        if (empty($this->in["email"]) || !is_string($this->in["email"])) {
            return Splash::log()->errNull("ErrLocalFieldMissing", __CLASS__, __FUNCTION__, "email");
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

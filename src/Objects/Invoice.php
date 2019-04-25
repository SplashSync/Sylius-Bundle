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

namespace Splash\Sylius\Objects;

use Doctrine\ORM\EntityManagerInterface as Manager;
use Splash\Bundle\Models\AbstractStandaloneObject;
use Splash\Client\Splash;
use Splash\Models\Objects\GenericFieldsTrait;
use Splash\Models\Objects\IntelParserTrait;
use Splash\Models\Objects\ListsTrait;
use Splash\Models\Objects\PricesTrait;
use Splash\Models\Objects\SimpleFieldsTrait;
use Splash\Sylius\Helpers\AddressAwareTrait;
use Splash\Sylius\Helpers\ChannelsAwareTrait;
use Splash\Sylius\Helpers\CustomersAwareTrait;
use Sylius\Bundle\ChannelBundle\Doctrine\ORM\ChannelRepository as Channels;
use Sylius\Bundle\CoreBundle\Doctrine\ORM\AddressRepository as Addresses;
use Sylius\Bundle\CoreBundle\Doctrine\ORM\CustomerRepository as Customers;
use Sylius\Bundle\CoreBundle\Doctrine\ORM\OrderRepository as Orders;
use Sylius\Component\Order\Model\OrderInterface;
use Sylius\Component\Resource\Factory\Factory;

/**
 * Sylius Invoice Object
 * 
 * @SuppressWarnings(PHPMD.CamelCasePropertyName)
 */
class Invoice extends Order
{
    //====================================================================//
    // Object Definition Parameters
    //====================================================================//

    /**
     *  Object Name (Translated by Module)
     */
    protected static $NAME = "Invoice";

    /**
     *  Object Description (Translated by Module).
     */
    protected static $DESCRIPTION = 'Sylius Invoice Object';

    /**
     *  Object Icon (FontAwesome or Glyph ico tag).
     */
    protected static $ICO = 'fa fa-money';

    /**
     *  Object Synchronistion Limitations
     *
     *  This Flags are Used by Splash Server to Prevent Unexpected Operations on Remote Server
     */
    // Allow Creation Of New Local Objects
    protected static $ALLOW_PUSH_CREATED = false;       
    // Allow Update Of Existing Local Objects
    protected static $ALLOW_PUSH_UPDATED = false;       
    // Allow Delete Of Existing Local Objects
    protected static $ALLOW_PUSH_DELETED = false;       
    
    /**
     *  Object Synchronistion Recommended Configuration
     */
    // Enable Creation Of New Local Objects when Not Existing
    protected static $ENABLE_PUSH_CREATED = false;
    // Enable Update Of Existing Local Objects when Modified Remotly
    protected static $ENABLE_PUSH_UPDATED = false;
    // Enable Delete Of Existing Local Objects when Deleted Remotly
    protected static $ENABLE_PUSH_DELETED = false;

    //====================================================================//
    // Private variables
    //====================================================================//

    /**
     * @var OrderInterface
     */
    protected $object;

    /**
     * @var Factory
     */
    protected $factory;

    //====================================================================//
    // Service Constructor
    //====================================================================//

    /**
     * Service Constructor
     *
     * @param Orders                 $repository
     * @param EntityManagerInterface $entityManager
     * @param CustomerRepository     $repository
     * @param Factory                $factory
     */
    public function __construct(Orders $repository, Channels $channels, Customers $customer, Addresses $address, Manager $manager, Factory $factory, array $configuration)
    {
        //====================================================================//
        // Link to Orders Repository
        $this->repository = $repository;
        //====================================================================//
        // Setup Sylius Channels Repository
        $this->setChannelsRepository($channels, $configuration);
        //====================================================================//
        // Setup Sylius Address Repository
        $this->setAddressRepository($address);
        //====================================================================//
        // Setup Sylius Customers Repository
        $this->setCustomersRepository($customer);
        //====================================================================//
        // Link to Doctrine Entity Manager Services
        $this->entityManager = $manager;
        //====================================================================//
        // Link to Customers Factory
        $this->factory = $factory;
    }
}

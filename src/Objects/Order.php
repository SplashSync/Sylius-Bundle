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

namespace Splash\SyliusSplashPlugin\Objects;

use Doctrine\ORM\EntityManagerInterface as Manager;
use SM\Factory\Factory as SmFactory;
use Splash\Bundle\Models\AbstractStandaloneObject;
use Splash\Client\Splash;
use Splash\Models\Objects\GenericFieldsTrait;
use Splash\Models\Objects\IntelParserTrait;
use Splash\Models\Objects\ListsTrait;
use Splash\Models\Objects\PricesTrait;
use Splash\Models\Objects\PrimaryKeysAwareInterface;
use Splash\Models\Objects\SimpleFieldsTrait;
use Splash\SyliusSplashPlugin\Helpers\AddressAwareTrait;
use Splash\SyliusSplashPlugin\Helpers\ChannelsAwareTrait;
use Splash\SyliusSplashPlugin\Helpers\CustomersAwareTrait;
use Sylius\Bundle\ChannelBundle\Doctrine\ORM\ChannelRepository as Channels;
use Sylius\Bundle\CoreBundle\Doctrine\ORM\AddressRepository as Addresses;
use Sylius\Bundle\CoreBundle\Doctrine\ORM\CustomerRepository as Customers;
use Sylius\Bundle\CoreBundle\Doctrine\ORM\OrderRepository as Orders;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

/**
 * Sylius Order Object
 *
 * @property Orders $repository
 */
class Order extends AbstractStandaloneObject implements PrimaryKeysAwareInterface
{
    use AddressAwareTrait;
    use CustomersAwareTrait;
    use ChannelsAwareTrait;

    // Splash Php Core Traits
    use IntelParserTrait;
    use SimpleFieldsTrait;
    use ListsTrait;
    use PricesTrait;
    use GenericFieldsTrait;

    // Common Traits
    use Common\TimestampTrait;
    use Common\ChannelTrait;

    // Order Traits
    use Order\CrudTrait;
    use Order\PrimaryTrait;
    use Order\CoreTrait;
    use Order\StatusMetaTrait;
    use Order\ItemsTrait;
    use Order\PaymentsTrait;
    use Order\StatusOrderTrait;
    use Order\StatusShippingTrait;
    use Order\DeliveryTrait;
    use Order\TotalsTrait;
    use Order\ShipmentTrait;
    use Order\ShipmentsTrait;
    use Order\ObjectsListTrait;

    //====================================================================//
    // Object Definition Parameters
    //====================================================================//

    /**
     * {@inheritdoc}
     */
    protected static string $name = "Order";

    /**
     * {@inheritdoc}
     */
    protected static string $description = 'Sylius Order Object';

    /**
     * {@inheritdoc}
     */
    protected static string $ico = 'fa fa-shopping-cart';

    /**
     * {@inheritdoc}
     */
    protected static bool $enablePushCreated = false;

    /**
     * {@inheritdoc}
     */
    protected static bool $enablePushDeleted = false;

    //====================================================================//
    // Private variables
    //====================================================================//

    /**
     * @phpstan-var OrderInterface
     */
    protected object $object;

    /**
     * @var FactoryInterface
     */
    protected FactoryInterface $factory;

    /**
     * @var SmFactory
     */
    private SmFactory $stateMachine;

    //====================================================================//
    // Service Constructor
    //====================================================================//

    /**
     * Service Constructor
     *
     * @param Orders           $repository
     * @param Channels         $channels
     * @param Customers        $customer
     * @param Addresses        $address
     * @param Manager          $manager
     * @param FactoryInterface $factory
     * @param SmFactory        $stateMachine
     * @param array            $configuration
     */
    public function __construct(
        Orders $repository,
        Channels $channels,
        Customers $customer,
        Addresses $address,
        Manager $manager,
        FactoryInterface $factory,
        SmFactory $stateMachine,
        array $configuration
    ) {
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
        // Setup Sylius State Machine
        $this->stateMachine = $stateMachine;
        //====================================================================//
        // Link to Doctrine Entity Manager Services
        $this->entityManager = $manager;
        //====================================================================//
        // Link to Customers Factory
        $this->factory = $factory;
    }

    /**
     * Check if Logistic Mode is Enabled
     */
    protected function isLogisticMode(): bool
    {
        return !($this instanceof Invoice)
            && !empty($this->getParameter("logistic", false))
        ;
    }
}

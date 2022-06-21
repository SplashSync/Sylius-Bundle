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
use Splash\Bundle\Models\AbstractStandaloneObject;
use Splash\Client\Splash;
use Splash\Models\Objects\GenericFieldsTrait;
use Splash\Models\Objects\IntelParserTrait;
use Splash\Models\Objects\ListsTrait;
use Splash\Models\Objects\PricesTrait;
use Splash\Models\Objects\SimpleFieldsTrait;
use Splash\SyliusSplashPlugin\Helpers\AddressAwareTrait;
use Splash\SyliusSplashPlugin\Helpers\ChannelsAwareTrait;
use Splash\SyliusSplashPlugin\Helpers\CustomersAwareTrait;
use Sylius\Bundle\ChannelBundle\Doctrine\ORM\ChannelRepository as Channels;
use Sylius\Bundle\CoreBundle\Doctrine\ORM\AddressRepository as Addresses;
use Sylius\Bundle\CoreBundle\Doctrine\ORM\CustomerRepository as Customers;
use Sylius\Bundle\CoreBundle\Doctrine\ORM\OrderRepository as Orders;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Resource\Factory\Factory;

/**
 * Sylius Order Object
 *
 * @SuppressWarnings(PHPMD.CamelCasePropertyName)
 */
class Order extends AbstractStandaloneObject
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

    // Order Traits
    use Order\CrudTrait;
    use Order\CoreTrait;
    use Order\MetaTrait;
    use Order\ItemsTrait;
    use Order\PaymentsTrait;
    use Order\StatusTrait;
    use Order\TotalsTrait;
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
     * @var Factory
     */
    protected Factory $factory;

    //====================================================================//
    // Service Constructor
    //====================================================================//

    /**
     * Service Constructor
     *
     * @param Orders    $repository
     * @param Channels  $channels
     * @param Customers $customer
     * @param Addresses $address
     * @param Manager   $manager
     * @param Factory   $factory
     * @param array     $configuration
     */
    public function __construct(
        Orders $repository,
        Channels $channels,
        Customers $customer,
        Addresses $address,
        Manager $manager,
        Factory $factory,
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
        // Link to Doctrine Entity Manager Services
        $this->entityManager = $manager;
        //====================================================================//
        // Link to Customers Factory
        $this->factory = $factory;
    }
}

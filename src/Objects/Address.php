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

namespace Splash\SyliusSplashPlugin\Objects;

use Doctrine\ORM\EntityManagerInterface;
use Splash\Bundle\Models\AbstractStandaloneObject;
use Splash\Models\Objects\GenericFieldsTrait;
use Splash\Models\Objects\IntelParserTrait;
use Splash\Models\Objects\ListsTrait;
use Splash\Models\Objects\SimpleFieldsTrait;
use Sylius\Bundle\CoreBundle\Doctrine\ORM\AddressRepository;
use Sylius\Bundle\CoreBundle\Doctrine\ORM\CustomerRepository;
use Sylius\Component\Core\Factory\AddressFactory as Factory;
use Sylius\Component\Core\Model\AddressInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Sylius Address Object
 */
class Address extends AbstractStandaloneObject
{
    // Splash Php Core Traits
    use IntelParserTrait;
    use SimpleFieldsTrait;
    use ListsTrait;
    use GenericFieldsTrait;

    // Sylius Address Traits
    use Address\CrudTrait;
    use Address\ObjectsListTrait;
    use Address\CoreTrait;
//    use Address\MainTrait;

    //====================================================================//
    // Object Definition Parameters
    //====================================================================//

    /**
     * {@inheritdoc}
     */
    protected static string $name = "Address";

    /**
     * {@inheritdoc}
     */
    protected static string $description = 'Sylius Address Object';

    /**
     * {@inheritdoc}
     */
    protected static string $ico = 'fa fa-envelope';

    /**
     * {@inheritdoc}
     */
    protected static bool $enablePushCreated = false;

    //====================================================================//
    // Private variables
    //====================================================================//

    /**
     * @phpstan-var AddressInterface
     */
    protected object $object;

    /**
     * @var TranslatorInterface
     */
    protected TranslatorInterface $translator;

    /**
     * @var CustomerRepository
     */
    protected CustomerRepository $customers;

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
     * @param TranslatorInterface    $trans
     * @param EntityManagerInterface $manager
     * @param AddressRepository      $repo
     * @param Factory                $factory
     * @param CustomerRepository     $customers
     */
    public function __construct(
        TranslatorInterface $trans,
        EntityManagerInterface $manager,
        AddressRepository $repo,
        Factory $factory,
        CustomerRepository $customers
    ) {
        //====================================================================//
        // Link to Symfony Translator
        $this->translator = $trans;
        //====================================================================//
        // Link to Doctrine Entity Manager Services
        $this->entityManager = $manager;
        //====================================================================//
        // Link to Address Repository
        $this->repository = $repo;
        //====================================================================//
        // Link to Address Factory
        $this->factory = $factory;
        //====================================================================//
        // Link to Customer Repository
        $this->customers = $customers;
    }
}

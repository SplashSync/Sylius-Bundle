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
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Sylius Address Object
 *
 * @SuppressWarnings(PHPMD.CamelCasePropertyName)
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
     *  Object Disable Flag. Uncomment thius line to Override this flag and disable Object.
     */
//    protected static    $DISABLED        =  True;

    /**
     *  Object Name (Translated by Module)
     */
    protected static $NAME = "Address";

    /**
     *  Object Description (Translated by Module).
     */
    protected static $DESCRIPTION = 'Sylius Address Object';

    /**
     *  Object Icon (FontAwesome or Glyph ico tag).
     */
    protected static $ICO = 'fa fa-envelope';

    /**
     * Enable Creation Of New Local Objects when Not Existing
     *
     * @codingStandardsIgnoreStart
     */
    protected static $ENABLE_PUSH_CREATED = false;
    /** @codingStandardsIgnoreEnd */

    //====================================================================//
    // Private variables
    //====================================================================//

    /**
     * @var AddressInterface
     */
    protected $object;

    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * @var CustomerRepository
     */
    protected $customers;

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
     * @param TranslatorInterface    $trans
     * @param EntityManagerInterface $manager
     * @param AddressRepository      $repo
     * @param Factory                $factory
     * @param CustomerRepository     $customers
     */
    public function __construct(TranslatorInterface $trans, EntityManagerInterface $manager, AddressRepository $repo, Factory $factory, CustomerRepository $customers)
    {
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

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

use Doctrine\ORM\EntityManagerInterface;
use Splash\Bundle\Models\AbstractStandaloneObject;
use Splash\Models\Objects\GenericFieldsTrait;
use Splash\Models\Objects\IntelParserTrait;
use Splash\Models\Objects\ListsTrait;
use Splash\Models\Objects\SimpleFieldsTrait;
use Sylius\Bundle\CoreBundle\Doctrine\ORM\CustomerRepository;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Resource\Factory\Factory;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Sylius Customer Object
 */
class ThirdParty extends AbstractStandaloneObject
{
    // Splash Php Core Traits
    use IntelParserTrait;
    use SimpleFieldsTrait;
    use ListsTrait;
    use GenericFieldsTrait;

    // ThirdParty Traits
    use ThirdParty\CrudTrait;
    use ThirdParty\ObjectsListTrait;
    use ThirdParty\CoreTrait;
    use ThirdParty\MainTrait;

    //====================================================================//
    // Object Definition Parameters
    //====================================================================//

    /**
     * {@inheritdoc}
     */
    protected static string $name = "Customer";

    /**
     * {@inheritdoc}
     */
    protected static string $description = 'Sylius Customer Object';

    /**
     * {@inheritdoc}
     */
    protected static string $ico = 'fa fa-user';

    /**
     * {@inheritdoc}
     */
    protected static bool $enablePushCreated = false;

    //====================================================================//
    // Private variables
    //====================================================================//

    /**
     * @phpstan-var CustomerInterface
     */
    protected object $object;

    /**
     * @var TranslatorInterface
     */
    protected TranslatorInterface $translator;

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
     * @param TranslatorInterface    $translator
     * @param EntityManagerInterface $entityManager
     * @param CustomerRepository     $repository
     * @param Factory                $factory
     */
    public function __construct(
        TranslatorInterface $translator,
        EntityManagerInterface $entityManager,
        CustomerRepository $repository,
        Factory $factory
    ) {
        //====================================================================//
        // Link to Symfony Translator
        $this->translator = $translator;
        //====================================================================//
        // Link to Doctrine Entity Manager Services
        $this->entityManager = $entityManager;
        //====================================================================//
        // Link to Customers Repository
        $this->repository = $repository;
        //====================================================================//
        // Link to Customers Factory
        $this->factory = $factory;
    }
}

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

namespace Splash\Sylius\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Splash\Bundle\Connectors\Standalone;
// Sylius Product Addictionnal Class to Monitor
use Splash\Bundle\Services\ConnectorsManager;
use Splash\Client\Splash;
use Sylius\Component\Core\Model\ChannelPricing;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\Product;
use Sylius\Component\Product\Model\ProductTranslation;

class ObjectEventListener
{
    const MANAGED_ENTITIES = array(
        "Address" => AddressInterface::class,
        "ThirdParty" => CustomerInterface::class
    );

    /**
     * Splash Connectors Manager
     *
     * @var ConnectorsManager
     */
    private $manager;

    //====================================================================//
    //  CONSTRUCTOR
    //====================================================================//

    /**
     * Service Constructor
     *
     * @param ConnectorsManager $manager
     */
    public function __construct(ConnectorsManager $manager)
    {
        //====================================================================//
        // Store Faker Connector Manager
        $this->manager = $manager;
    }

    /**
     * On Entity Created Doctrine Event
     *
     * @param LifecycleEventArgs $eventArgs
     */
    public function postPersist(LifecycleEventArgs $eventArgs): void
    {
        //====================================================================//
        // Check if Entity is managed by Splash Sylius Bundle
        $objectType = $this->isManagedEntity($eventArgs);
        if (null == $objectType) {
            return;
        }
        //====================================================================//
        // Get Impacted Object Id
        $objectId = $eventArgs->getEntity()->getId();
        //====================================================================//
        // Do Object Change Commit
        $this->doCommit($objectType, $objectId, SPL_A_CREATE);
    }

    /**
     * On Entity Updated Doctrine Event
     *
     * @param LifecycleEventArgs $eventArgs
     */
    public function postUpdate(LifecycleEventArgs $eventArgs): void
    {
        //====================================================================//
        // Check if Entity is managed by Splash Sylius Bundle
        $objectType = $this->isManagedEntity($eventArgs);
        if (null == $objectType) {
            return;
        }
        //====================================================================//
        // Get Impacted Object
        $entity = $eventArgs->getEntity();
        //====================================================================//
        // Get Impacted Object Id
        $objectId = $entity->getId();

        //====================================================================//
        // Update on Product Main
        if (is_a($entity, Product::class)) {
            if (Splash::Object('Product')->isLocked('Base-'.$entity->getId())) {
                return;
            }
            $EntityIds = array();
            foreach ($entity->getVariants() as $Variant) {
                $EntityIds[] = $Variant->getId();
            }
            $this->doCommit('Product', $EntityIds, SPL_A_UPDATE);
            Splash::Object('Product')->Lock('Base-'.$entity->getId());

            return;
        }

        //====================================================================//
        // Update on Product Translations
        if (is_a($entity, ProductTranslation::class)) {
            $Product = $entity->getTranslatable();
            if (Splash::Object('Product')->isLocked('Base-'.$Product->getId())) {
                return;
            }
            $EntityIds = array();
            foreach ($entity->getTranslatable()->getVariants() as $Variant) {
                $EntityIds[] = $Variant->getId();
            }
            $this->doCommit('Product', $EntityIds, SPL_A_UPDATE);
            Splash::Object('Product')->Lock('Base-'.$entity->getTranslatable()->getId());

            return;
        }

        //====================================================================//
        // Update on Product Channel Price
        if (is_a($entity, ChannelPricing::class)) {
            $objectId = $entity->getProductVariant()->getId();
        }

        //====================================================================//
        // Commit Object Change
        $this->doCommit($objectType, $objectId, SPL_A_UPDATE);
    }

    /**
     * On Entity Before Deleted Doctrine Event
     *
     * @param LifecycleEventArgs $eventArgs
     */
    public function preRemove(LifecycleEventArgs $eventArgs): void
    {
        //====================================================================//
        // Check if Entity is managed by Splash Sylius Bundle
        $objectType = $this->isManagedEntity($eventArgs);
        if (null == $objectType) {
            return;
        }
        //====================================================================//
        // Get Impacted Object Id
        $objectId = $eventArgs->getEntity()->getId();
        //====================================================================//
        // Do Object Change Commit
        $this->doCommit($objectType, $objectId, SPL_A_DELETE);
    }

    /**
     * Execut Splahs Commit for Sylius Objects
     *
     * @param string       $objectType
     * @param array|string $objectIds
     * @param string       $action
     */
    private function doCommit(string $objectType, $objectIds, string $action): void
    {
        //====================================================================//
        // Safety Check
        if (empty($objectIds)) {
            return;
        }
        if (!is_scalar($objectIds) && !is_array($objectIds)) {
            return;
        }
        //====================================================================//
        // Locked (Just created) => Skip
        if ((SPL_A_UPDATE == $action) && Splash::Object($objectType)->isLocked()) {
            return;
        }        
        //====================================================================//
        //  Search in Configured Servers using Standalone Connector
        $servers = $this->manager->getConnectorConfigurations(Standalone::NAME);
        //====================================================================//
        //  Walk on Configured Servers
        foreach (array_keys($servers) as $serverId) {
            //====================================================================//
            //  Load Connector
            $connector = $this->manager->get((string) $serverId);
            //====================================================================//
            //  Safety Check
            if (null === $connector) {
                continue;
            }
            //====================================================================//
            //  Execute Commit
            $connector->commit(
                $objectType,
                $objectIds,
                $action,
                'Sylius Bundle',
                'Change Commited on Sylius for '.$objectType
            );
        }
        //====================================================================//
        // Catch Splash Logs
        $this->manager->pushLogToSession(true);

    }

    /**
     * Check if Entity is managed by Splash Sylius Bundle
     * Also Detect Entity Type Name
     *
     * @param LifecycleEventArgs $eventArgs
     *
     * @return bool
     */
    private function isManagedEntity(LifecycleEventArgs $eventArgs): ?string
    {
        //====================================================================//
        // Touch Impacted Entity
        $entity = $eventArgs->getEntity();
        //====================================================================//
        // Walk on Managed Entities
        foreach (self::MANAGED_ENTITIES as $objectType => $entityClass) {
            if (is_a($entity, $entityClass)) {
                return $objectType;
            }
        }

        return null;
    }
}

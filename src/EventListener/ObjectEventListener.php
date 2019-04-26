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
use Sylius\Component\Core\Model\ChannelPricingInterface;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Product\Model\ProductTranslationInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Model\OrderInterface;

class ObjectEventListener
{
    /**
     * List of Entities Managed by Splash Sylius Module
     * 
     * @var array
     */
    const MANAGED_ENTITIES = array(
        AddressInterface::class => "Address",
        CustomerInterface::class => "ThirdParty",
        ProductVariantInterface::class => "Product",
        OrderInterface::class => "Order",
    );
    
    /**
     * List of Connected Entities Managed by Splash Sylius Module
     * 
     * @var array
     */
    const CONNECTED_ENTITIES = array(
        ProductInterface::class => "Product",
        ProductTranslationInterface::class => "Product",
        ChannelPricingInterface::class => "Product",
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
        $objectType = $this->isManagedEntity($eventArgs, false);
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
        $objectType = $this->isManagedEntity($eventArgs, true);
        if (null == $objectType) {
            return;
        }
        //====================================================================//
        // Get Impacted Object Ids
        $objectIds = $this->getEntityIds($eventArgs);
        if (null == $objectIds) {
            return;
        }
        //====================================================================//
        // Commit Object Change
        $this->doCommit($objectType, $objectIds, SPL_A_UPDATE);
        //====================================================================//
        // After Updates on Product
        if (is_a($eventArgs->getEntity(), ProductInterface::class)) {
            Splash::Object('Product')->lock('Base-'.$eventArgs->getEntity()->getId());
        }         
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
        $objectType = $this->isManagedEntity($eventArgs, false);
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
            //  Prepare Commit Parameters
            $user = 'Sylius Bundle';
            $msg = 'Change Commited on Sylius for '.$objectType;
            //====================================================================//
            //  Execute Commit
            $connector->commit($objectType, $objectIds, $action, $user, $msg);
            if($objectType == "Order") {
                $connector->commit("Invoice", $objectIds, $action, $user, $msg);
            }
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
     * @param bool $connected
     *
     * @return string
     */
    private function isManagedEntity(LifecycleEventArgs $eventArgs, bool $connected): ?string
    {
        //====================================================================//
        // Touch Impacted Entity
        $entity = $eventArgs->getEntity();
        //====================================================================//
        // Walk on Managed Entities
        foreach (self::MANAGED_ENTITIES as $entityClass => $objectType) {
            if (is_a($entity, $entityClass)) {
                return $objectType;
            }
        }
        //====================================================================//
        // Walk on Connected Entities
        if ($connected) {
            foreach (self::CONNECTED_ENTITIES as $entityClass => $objectType) {
                if (is_a($entity, $entityClass)) {
                    return $objectType;
                }
            }
        }

        return null;
    }
    
    /**
     * Check if Entity is managed by Splash Sylius Bundle
     * Also Detect Entity Type Name
     *
     * @param LifecycleEventArgs $eventArgs
     *
     * @return null|array
     */
    private function getEntityIds(LifecycleEventArgs $eventArgs): ?array
    {
        //====================================================================//
        // Get Impacted Object
        $entity = $eventArgs->getEntity();
        //====================================================================//
        // Get Impacted Object Id
        $objectIds = array($entity->getId());
        //====================================================================//
        // Update on Product Translations
        if (is_a($entity, ProductTranslationInterface::class)) {
            $entity = $entity->getTranslatable();
        }
        //====================================================================//
        // Update on Product Main
        if (is_a($entity, ProductInterface::class)) {
            if (Splash::Object('Product')->isLocked('Base-'.$entity->getId())) {
                return null;
            }
            $objectIds = array();
            foreach ($entity->getVariants() as $variant) {
                $objectIds[] = $variant->getId();
            }
        }
        //====================================================================//
        // Update on Product Channel Price
        if (is_a($entity, ChannelPricingInterface::class)) {
            $objectIds = array($entity->getProductVariant()->getId());
        }

        return $objectIds;
    }    
}

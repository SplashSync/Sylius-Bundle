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

namespace Splash\SyliusSplashPlugin\EventListener;

use Doctrine\Persistence\Event\LifecycleEventArgs;
use Exception;
use Splash\Bundle\Connectors\Standalone;
use Splash\Bundle\Models\AbstractConnector;
use Splash\Bundle\Services\ConnectorsManager;
use Splash\Client\Splash;
use Splash\Local\Local;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\ChannelPricingInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Product\Model\ProductTranslationInterface;

/**
 * Sylius Bundle Doctrine Entity Changes Event Listener
 *
 * Catch changes on Entities & Sends Commits to Splash
 */
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
    private ConnectorsManager $manager;

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
     *
     * @throws Exception
     */
    public function postPersist(LifecycleEventArgs $eventArgs): void
    {
        $this->doGenericCommit($eventArgs, SPL_A_CREATE);
    }

    /**
     * On Entity Updated Doctrine Event
     *
     * @param LifecycleEventArgs $eventArgs
     *
     * @throws Exception
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
        //  Search in Configured Servers using Standalone Connector
        $servers = $this->manager->getConnectorConfigurations(Standalone::NAME);
        //====================================================================//
        //  Walk on Configured Servers
        foreach (array_keys($servers) as $serverId) {
            //====================================================================//
            //  Setup Splash Local Class
            $this->getLocalClass()->setServerId($serverId);
            //====================================================================//
            // Get Impacted Object Ids
            $objectIds = $this->getEntityIds($eventArgs);
            if (null == $objectIds) {
                return;
            }
            //====================================================================//
            // Commit Object Change
            $this->doCommit($this->getLocalClass()->getConnector(), $objectType, $objectIds, SPL_A_UPDATE);
            //====================================================================//
            // After Updates on Product
            if (is_a($object = $eventArgs->getObject(), ProductInterface::class)) {
                Splash::object('Product')->lock('Base-'.$object->getId());
            }
        }
        //====================================================================//
        // Catch Splash Logs
        $this->manager->pushLogToSession(true);
    }

    /**
     * On Entity Before Deleted Doctrine Event
     *
     * @throws Exception
     */
    public function preRemove(LifecycleEventArgs $eventArgs): void
    {
        $this->doGenericCommit($eventArgs, SPL_A_DELETE);
    }

    /**
     * @param LifecycleEventArgs $eventArgs
     * @param string             $action
     *
     * @throws Exception
     */
    private function doGenericCommit(LifecycleEventArgs $eventArgs, string $action): void
    {
        //====================================================================//
        // Check if Entity is managed by Splash Sylius Bundle
        $objectType = $this->isManagedEntity($eventArgs, false);
        if (null == $objectType) {
            return;
        }
        //====================================================================//
        //  Search in Configured Servers using Standalone Connector
        $servers = $this->manager->getConnectorConfigurations(Standalone::NAME);
        //====================================================================//
        //  Walk on Configured Servers
        foreach (array_keys($servers) as $serverId) {
            //====================================================================//
            //  Setup Splash Local Class
            $this->getLocalClass()->setServerId($serverId);
            //====================================================================//
            // Do Object Change Commit
            $this->doCommit(
                $this->getLocalClass()->getConnector(),
                $objectType,
                $this->getEventEntityId($eventArgs),
                $action
            );
        }
        //====================================================================//
        // Catch Splash Logs
        $this->manager->pushLogToSession(true);
    }

    /**
     * Execute Splash Commit for Sylius Objects
     *
     * @param AbstractConnector $connector
     * @param string            $objectType
     * @param array|string      $objectIds
     * @param string            $action
     *
     * @throws Exception
     */
    private function doCommit(AbstractConnector $connector, string $objectType, $objectIds, string $action): void
    {
        //====================================================================//
        // Safety Check
        if (empty($objectIds)) {
            return;
        }
        //====================================================================//
        // Locked (Just created) => Skip
        if ((SPL_A_UPDATE == $action) && Splash::object($objectType)->isLocked()) {
            return;
        }
        //====================================================================//
        //  Prepare Commit Parameters
        $user = 'Sylius Bundle';
        $msg = 'Change Committed on Sylius for '.$objectType;
        //====================================================================//
        //  Execute Commit
        $connector->commit($objectType, $objectIds, $action, $user, $msg);
        if ($this->isInvoiceCommitRequired($objectType, $objectIds, $action)) {
            $connector->commit("Invoice", $objectIds, $action, $user, $msg);
        }
    }

    /**
     * Check if Entity is managed by Splash Sylius Bundle
     * Also Detect Entity Type Name
     *
     * @param LifecycleEventArgs $eventArgs
     * @param bool               $connected
     *
     * @return string
     */
    private function isManagedEntity(LifecycleEventArgs $eventArgs, bool $connected): ?string
    {
        //====================================================================//
        // Touch Impacted Entity
        $entity = $eventArgs->getObject();
        //====================================================================//
        // Walk on Managed Entities
        foreach (self::MANAGED_ENTITIES as $entityClass => $objectType) {
            if (is_a($entity, $entityClass) && !$this->isFiltered($objectType, $entity)) {
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
     * Safe Get Event Doctrine Entity ID
     *
     * @param LifecycleEventArgs $eventArgs
     *
     * @throws Exception
     *
     * @return string
     */
    private function getEventEntityId(LifecycleEventArgs $eventArgs): string
    {
        //====================================================================//
        // Get Impacted Object Id
        $entity = $eventArgs->getObject();
        //====================================================================//
        // Safety Check
        if (!method_exists($entity, "getId")) {
            throw new Exception("Sylius Managed Entity is Invalid, no Id getter exists.");
        }

        return (string) $entity->getId();
    }

    /**
     * Check if Entity is managed by Splash Sylius Bundle
     * Also Detect Entity Type Name
     *
     * @param LifecycleEventArgs $eventArgs
     *
     * @throws Exception
     *
     * @return null|array
     */
    private function getEntityIds(LifecycleEventArgs $eventArgs): ?array
    {
        //====================================================================//
        // Get Impacted Object
        $entity = $eventArgs->getObject();
        //====================================================================//
        // Get Impacted Object Id
        $objectIds = array($this->getEventEntityId($eventArgs));
        //====================================================================//
        // Update on Product Translations
        if (is_a($entity, ProductTranslationInterface::class)) {
            $entity = $entity->getTranslatable();
        }
        //====================================================================//
        // Update on Product Main
        if (is_a($entity, ProductInterface::class)) {
            if (Splash::object('Product')->isLocked('Base-'.$entity->getId())) {
                return null;
            }
            $objectIds = array();
            foreach ($entity->getVariants() as $variant) {
                $objectIds[] = $variant->getId();
            }
            krsort($objectIds);
        }
        //====================================================================//
        // Update on Product Channel Price
        if (is_a($entity, ChannelPricingInterface::class)) {
            $variant = $entity->getProductVariant();
            if (null == $variant) {
                return null;
            }
            $objectIds = array($variant->getId());
        }

        return $objectIds;
    }

    /**
     * Get Splash Bundle Local Class
     *
     * @throws Exception
     *
     * @return Local
     */
    private function getLocalClass(): Local
    {
        /** @phpstan-ignore-next-line  */
        return Splash::local();
    }

    /**
     * Check if Object Should be Filtered
     *
     * @param string $objectType
     * @param object $object
     *
     * @return bool
     */
    private function isFiltered(string $objectType, object $object): bool
    {
        //====================================================================//
        // No Filtering on CI/CD Testing
        if (Splash::isTravisMode()) {
            return false;
        }
        //====================================================================//
        // Disable Commits for Drafts Order
        if (('Order' == $objectType) && ($object instanceof OrderInterface)) {
            if (OrderInterface::STATE_CART == $object->getState()) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if Invoice Object Should be Committed Too
     *
     * @param string       $objectType
     * @param array|string $objectIds
     * @param string       $action
     *
     * @throws Exception
     *
     * @return bool
     */
    private function isInvoiceCommitRequired(string $objectType, $objectIds, string $action): bool
    {
        //====================================================================//
        // Entity is An Order
        if (("Order" != $objectType) || !is_scalar($objectIds)) {
            return false;
        }
        //====================================================================//
        // Order is Locked
        if (Splash::object($objectType)->isLocked($objectIds)) {
            return false;
        }
        if ((SPL_A_CREATE == $action)) {
            return false;
        }

        return true;
    }
}

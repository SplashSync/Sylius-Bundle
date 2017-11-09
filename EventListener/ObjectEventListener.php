<?php

namespace Splash\Sylius\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Splash\Client\Splash;

// Sylius Product Addictionnal Class to Monitor
use Sylius\Component\Core\Model\Product;
use Sylius\Component\Product\Model\ProductTranslation;
use Sylius\Component\Core\Model\ChannelPricing;

class ObjectEventListener
{
    
//    public function postPersist(LifecycleEventArgs $eventArgs)
//    {
//        $this->doCommit($eventArgs->getEntity(), SPL_A_CREATE);
//    }    
    
    public function postUpdate(LifecycleEventArgs $eventArgs)
    {
        //====================================================================//
        // Get Impacted Object 
        $Entity =   $eventArgs->getEntity();

        //====================================================================//
        // Update on Product Main
        if ( is_a($Entity, Product::class) ) {
            if (Splash::Object('Product')->isLocked('Base-' . $Entity->getId())) {
                return;
            }
            $EntityIds = array();
            foreach ($Entity->getVariants() as $Variant) {
                $EntityIds[] = $Variant->getId();
            }
            $this->doCommit('Product', $EntityIds, SPL_A_UPDATE);
            Splash::Object('Product')->Lock('Base-' . $Entity->getId());
        } 
        
        //====================================================================//
        // Update on Product Translations
        if ( is_a($Entity, ProductTranslation::class) ) {
            $Product    =   $Entity->getTranslatable();
            if (Splash::Object('Product')->isLocked('Base-' . $Product->getId())) {
                return;
            }
            $EntityIds = array();
            foreach ($Entity->getTranslatable()->getVariants() as $Variant) {
                $EntityIds[] = $Variant->getId();
            }
            $this->doCommit('Product', $EntityIds, SPL_A_UPDATE);
            Splash::Object('Product')->Lock('Base-' . $Entity->getTranslatable()->getId());
        }         
        
        //====================================================================//
        // Update on Product Channel Price
        if ( is_a($Entity, ChannelPricing::class) ) {
            $this->doCommit('Product', $Entity->getProductVariant()->getId(), SPL_A_UPDATE);
        }             
        
    }    

//    public function preRemove(LifecycleEventArgs $eventArgs)
//    {
//        $this->doCommit($eventArgs->getEntity(), SPL_A_DELETE);
//    }    

    private function doCommit($ObjectType, $EntityIds, $Action)
    {
        //====================================================================//
        // Safety Check 
        if ( empty($EntityIds) ) {
            return;
        }
        if ( !is_scalar($EntityIds) && !is_array($EntityIds) ) {
            return;
        }
        //====================================================================//
        // Commit Change to Server 
        Splash::Commit($ObjectType, $EntityIds, $Action, "Symfony", "Change Commited on Sylius");
        //====================================================================//
        // Render User Messages
        Splash::Local()->pushNotifications();
    }
    
}

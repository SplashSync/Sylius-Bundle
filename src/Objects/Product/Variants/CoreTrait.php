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

namespace Splash\Sylius\Objects\Product\Variants;

use Splash\Client\Splash;
use Sylius\Component\Core\Model\ProductInterface as Product;
use Sylius\Component\Core\Model\ProductVariantInterface as Variant;

/**
 * Sylius Produitc Variants Core Fields
 */
trait CoreTrait {
    
    /**
     * Build Fields using FieldFactory
     */
    protected function buildVariantsCoreFields()
    {
        //====================================================================//
        // Product Variation List - Product Link
        $this->fieldsFactory()->Create((string) self::objects()->Encode("Product", SPL_T_ID))
            ->Identifier("id")
            ->Name("Variants")
            ->InList("variants")
            ->MicroData("http://schema.org/Product", "Variants")
            ->isNotTested();
        
        //====================================================================//
        // Product Variation List - Product SKU
        $this->fieldsFactory()->Create(SPL_T_VARCHAR)
            ->Identifier("code")
            ->Name("SKU")
            ->InList("variants")
            ->MicroData("http://schema.org/Product", "VariationName")
            ->isReadOnly();
    }
    
    /**
     * Read requested Field
     *
     * @param string $key       Input List Key
     * @param string $fieldName Field Identifier / Name
     */
    protected function getVariantsCoreFields($key, $fieldName)
    {
        //====================================================================//
        // Check if List field & Init List Array
        $fieldId = self::lists()->initOutput($this->out, "variants", $fieldName);
        if (!$fieldId || !($this->product instanceof Product)) {
            return;
        }
        //====================================================================//
        // Load Product Variants
        /** @var Variant $variant */
        foreach ($this->product->getVariants() as $index => $variant) {
            //====================================================================//
            // SKIP Current Variant When in PhpUnit/Travis Mode
            if (!$this->isAllowedVariantChild($variant)) {
                continue;
            }
            
            //====================================================================//
            // Read Products Variants Infos
            switch ($fieldId) {
                //====================================================================//
                // Variant Readings
                case 'id':
                    $value = (string) self::objects()->encode("Product", $variant->getId());
                    break;
                //====================================================================//
                // Product Readings
                case 'code':
                    $value = $variant->getCode();

                    break;
            }            
            //====================================================================//
            // Add Variant Infos to List
            self::lists()->insert($this->out, "variants", $fieldId, $index, $value);
        }
        unset($this->in[$key]);
    }
    
    /**
     * Write Given Fields
     *
     * @param string $fieldName Field Identifier / Name
     * @param mixed  $fieldData Field Data
     */
    public function setVariantsCoreFields($fieldName, $fieldData)
    {
        if (isset($this->in["variants"])) {
            unset($this->in["variants"]);
        }        
    }    

    //====================================================================//
    // PRIVATE - Tooling Functions
    //====================================================================//
    
    /**
     * Check if Product Variant Should be Listed
     *
     * @param Variant $variant Combination Resume Array
     *
     * @return bool
     */
    private function isAllowedVariantChild(Variant $variant): bool
    {
        //====================================================================//
        // Not in PhpUnit/Travis Mode => Return All
        if (empty(Splash::input('SPLASH_TRAVIS'))) {
            return true;
        }
        //====================================================================//
        // Travis Mode => Skip Current Product Variant
        return ($variant->getId() != $this->object->getId());
    }    
}

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
use Sylius\Component\Product\Model\ProductOptionValueInterface as Option;

/**
 * Sylius Products Variants Attibutes Fields
 */
trait AttributesTrait
{
    //====================================================================//
    // Fields Generation Functions
    //====================================================================//

    /**
     * Build Attributes Fields using FieldFactory
     */
    protected function buildVariantsAttributesFields()
    {
        $groupName = "Attributes";
        $this->fieldsFactory()->setDefaultLanguage($this->translations->getDefaultLocaleCode());

        //====================================================================//
        // PRODUCT VARIANTS ATTRIBUTES
        //====================================================================//

        //====================================================================//
        // Product Variation Attribute Code (Default Language Only)
        $this->fieldsFactory()->create(SPL_T_VARCHAR)
            ->Identifier("code")
            ->name("Code")
            ->description("Attribute Code")
            ->InList("attributes")
            ->Group($groupName)
            ->addOption("isLowerCase", true)
            ->MicroData("http://schema.org/Product", "VariantAttributeCode")
            ->isNotTested();

        //====================================================================//
        // Walk on All Available Languages
        //====================================================================//
        // Product Variation Attribute Name
        $this->fieldsFactory()->create(SPL_T_VARCHAR)
            ->Identifier("name")
            ->Name("Name")
            ->description("Attribute Name")
            ->Group($groupName)
            ->MicroData("http://schema.org/Product", "VariantAttributeName")
            ->setDefaultLanguage("fr_FR")
            ->InList("attributes")
            ->isNotTested();
        //====================================================================//
        // Product Variation Attribute Value
        $this->fieldsFactory()->create(SPL_T_VARCHAR)
            ->Identifier("value")
            ->Name("Value")
            ->description("Attribute Value")
            ->Group($groupName)
            ->MicroData("http://schema.org/Product", "VariantAttributeValue")
            ->setDefaultLanguage("fr_FR")
            ->InList("attributes")
            ->isNotTested();
    }

    //====================================================================//
    // Fields Reading Functions
    //====================================================================//

    /**
     * Read requested Field
     *
     * @param string $key Input List Key
     * @param string $fieldName Field Identifier / Name
     */
    protected function getVariantsAttributesFields($key, $fieldName)
    {
        //====================================================================//
        // Check if List field & Init List Array
        $fieldId = self::lists()->initOutput($this->out, "attributes", $fieldName);
        if (!$fieldId) {
            return;
        }
        //====================================================================//
        // Walk on Available Attributes
        foreach ($this->object->getOptionValues() as $index => $optionValue) {
            //====================================================================//
            // Read Attribute Data
            $value = $this->getVariantsAttributesField($optionValue, $fieldId);
            //====================================================================//
            // Push Attribute Data to List
            self::lists()->insert($this->out, "attributes", $fieldId, $index, $value);
        }
        unset($this->in[$key]);
    }

    //====================================================================//
    // Fields Writting Functions
    //====================================================================//

    /**
     * Write Given Fields
     *
     * @param string $fieldName Field Identifier / Name
     * @param mixed $fieldData Field Data
     */
    protected function setVariantsAttributesFields($fieldName, $fieldData)
    {
        //====================================================================//
        // Check is Attribute Field
        if (("attributes" !== $fieldName)) {
            return false;
        }
        //====================================================================//
        // Identify Product Variant Attributes
        $optionsCodes = array();
        foreach ($fieldData as $attrItem) {
            //====================================================================//
            // Check Product Attributes are Valid
            if (!$this->attributes->isValidDefinition($attrItem)) {
                continue;
            }
            //====================================================================//
            // Store Attribute Code
            $optionsCodes[] = $attrItem["code"];
            //====================================================================//
            // Identify or Add Attribute
            $option = $this->attributes->touchProductOption($attrItem);
            if (empty($option)) {
                continue;
            }
            //====================================================================//
            // Update Attribute Names in Extra Languages
            $this->attributes->updateProductOption($option, $attrItem);
            //====================================================================//
            // Identify or Add Attribute Id
            $optionValue = $this->attributes->touchProductOptionValue($option, $attrItem);
            if (empty($optionValue)) {
                continue;
            }
            //====================================================================//
            // Update Attribute Value Names in Extra Languages
            $this->attributes->updateProductOptionValue($optionValue, $attrItem);
            //====================================================================//
            // Setup Product Variant Option Value
            if (!$this->object->hasOptionValue($optionValue)) {
                $this->attributes->updateVariantOptionValue($this->object, $optionValue);
                $this->needUpdate();
                $this->needUpdate("product");
            }
        }
        //====================================================================//
        // CleanUp Product Variant Attributes
        $this->attributes->cleanVariantOptionValues($this->object, $optionsCodes);

        unset($this->in[$fieldName]);
    }

    /**
     * Read requested Field
     *
     * @param Option $optionValue Sylius Product OptionValue Object
     * @param string $fieldName Field Identifier / Name
     *
     * @return null|string
     */
    private function getVariantsAttributesField(Option $optionValue, $fieldName): ?string
    {
        $option = $optionValue->getOption();
        if (empty($option)) {
            return null;
        }
        //====================================================================//
        // Walk on All Available Languages
        foreach ($this->translations->getLocales() as $locale) {
            //====================================================================//
            // Decode Multilang Field Name
            $baseFieldName = $this->translations->fieldNameDecode($locale, $fieldName);
            //====================================================================//
            // Read Attribute Data
            switch ($baseFieldName) {
                case 'code':
                    return $option->getCode();
                case 'name':
                    return $option->getTranslation($locale->getCode())->getName();
                case 'value':
                    return $optionValue->getTranslation($locale->getCode())->getValue();
            }
        }

        return null;
    }
}

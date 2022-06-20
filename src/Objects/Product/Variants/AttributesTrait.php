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

namespace Splash\SyliusSplashPlugin\Objects\Product\Variants;

use Sylius\Component\Core\Model\ProductInterface as Product;
use Sylius\Component\Product\Model\ProductOptionValueInterface as Option;

/**
 * Sylius Products Variants Attributes Fields
 */
trait AttributesTrait
{
    //====================================================================//
    // Fields Generation Functions
    //====================================================================//

    /**
     * Build Attributes Fields using FieldFactory
     */
    protected function buildVariantsAttributesFields(): void
    {
        $groupName = "Attributes";
        $this->fieldsFactory()->setDefaultLanguage($this->translations->getDefaultLocaleCode());

        //====================================================================//
        // PRODUCT VARIANTS ATTRIBUTES
        //====================================================================//

        //====================================================================//
        // Product Variation Attribute Code (Default Language Only)
        $this->fieldsFactory()->create(SPL_T_VARCHAR)
            ->identifier("code")
            ->name("Code")
            ->description("Attribute Code")
            ->inList("attributes")
            ->group($groupName)
            ->addOption("isLowerCase", true)
            ->microData("http://schema.org/Product", "VariantAttributeCode")
            ->isNotTested()
        ;

        //====================================================================//
        // Walk on All Available Languages
        foreach ($this->translations->getLocales() as $locale) {
            //====================================================================//
            // Product Variation Attribute Name
            $this->fieldsFactory()->create(SPL_T_VARCHAR)
                ->identifier("name")
                ->name("Name")
                ->description("Attribute Name")
                ->microData("http://schema.org/Product", "VariantAttributeName")
                ->setMultilang($locale->getCode())
                ->inList("attributes")
                ->group($groupName)
                ->isNotTested()
            ;
            //====================================================================//
            // Product Variation Attribute Value
            $this->fieldsFactory()->create(SPL_T_VARCHAR)
                ->identifier("value")
                ->name("Value")
                ->description("Attribute Value")
                ->microData("http://schema.org/Product", "VariantAttributeValue")
                ->setMultilang($locale->getCode())
                ->inList("attributes")
                ->group($groupName)
                ->isNotTested()
            ;
        }
    }

    //====================================================================//
    // Fields Reading Functions
    //====================================================================//

    /**
     * Read requested Field
     *
     * @param string $key       Input List Key
     * @param string $fieldName Field Identifier / Name
     */
    protected function getVariantsAttributesFields(string $key, string $fieldName): void
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
    // Fields Writing Functions
    //====================================================================//

    /**
     * Write Given Fields
     *
     * @param string $fieldName Field Identifier / Name
     * @param array  $fieldData Field Data
     */
    protected function setVariantsAttributesFields(string $fieldName, array $fieldData): void
    {
        //====================================================================//
        // Check is Attribute Field
        if (("attributes" !== $fieldName)) {
            return;
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
            //====================================================================//
            // Update Attribute Names in Extra Languages
            $this->attributes->updateProductOption($option, $attrItem);
            //====================================================================//
            // Identify or Add Attribute Id
            $optionValue = $this->attributes->touchProductOptionValue($option, $attrItem);
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
     * @param string $fieldName   Field Identifier / Name
     *
     * @return null|string
     */
    private function getVariantsAttributesField(Option $optionValue, string $fieldName): ?string
    {
        $option = $optionValue->getOption();
        if (empty($option)) {
            return null;
        }
        //====================================================================//
        // Walk on All Available Languages
        foreach ($this->translations->getLocales()  as $locale) {
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

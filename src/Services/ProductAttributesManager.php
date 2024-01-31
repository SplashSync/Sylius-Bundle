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

namespace   Splash\SyliusSplashPlugin\Services;

use ArrayObject;
use Doctrine\ORM\EntityManagerInterface as Manager;
use Splash\Core\SplashCore      as Splash;
use Splash\SyliusSplashPlugin\Services\ProductTranslationsManager as Translations;
use Sylius\Bundle\ProductBundle\Doctrine\ORM\ProductOptionRepository as Options;
use Sylius\Component\Core\Model\ProductVariantInterface as Variant;
use Sylius\Component\Locale\Model\LocaleInterface;
use Sylius\Component\Product\Model\ProductOptionInterface;
use Sylius\Component\Product\Model\ProductOptionValueInterface;
use Sylius\Component\Resource\Factory\TranslatableFactory as Factory;

/**
 * Product Attributes Manager
 * Manage Access to Products Attributes Configurations
 */
class ProductAttributesManager
{
    /**
     * Doctrine Entity Manager
     *
     * @var Manager
     */
    protected Manager $manager;

    /**
     * @var Options
     */
    protected Options $options;

    /**
     * @var Factory
     */
    protected Factory $factory;

    /**
     * @var Factory
     */
    protected Factory $valuesFactory;

    /**
     * @var Translations
     */
    protected Translations $translations;

    /**
     * @var array
     */
    protected array $config;

    /**
     * List of Required Attributes Fields
     *
     * @var array
     */
    private static array $requiredFields = array(
        "code" => "Attribute Code",
        "name" => "Attribute Name",
        "value" => "Attribute Value Name",
    );

    /**
     * Service Constructor
     *
     * @param Manager      $manager
     * @param Options      $options
     * @param Factory      $factory
     * @param Factory      $values
     * @param Translations $translations
     * @param array        $configuration
     */
    public function __construct(
        Manager $manager,
        Options $options,
        Factory $factory,
        Factory $values,
        Translations $translations,
        array $configuration
    ) {
        //====================================================================//
        // Setup Products Options Repository
        $this->options = $options;
        //====================================================================//
        // Sylius Products Options Manager
        $this->manager = $manager;
        //====================================================================//
        // Sylius Products Options Factory
        $this->factory = $factory;
        //====================================================================//
        // Sylius Products Options Factory
        $this->valuesFactory = $values;
        //====================================================================//
        // Link to Splash Sylius Translations Manager
        $this->translations = $translations;
        //====================================================================//
        // Store Bundle Configuration
        $this->config = $configuration;
    }

    /**
     * Get Translated Product Attributes String
     *
     * @param Variant         $variant
     * @param LocaleInterface $locale
     *
     * @return string
     */
    public function getOptionsNameString(Variant $variant, LocaleInterface $locale): string
    {
        $attributesValues = array();
        //====================================================================//
        // Walk on Available Attributes
        foreach ($variant->getOptionValues() as $optionValue) {
            //====================================================================//
            // Read Attribute Value Name
            $attributesValues[] = $optionValue->getTranslation($locale->getCode())->getValue();
        }

        return empty($attributesValues) ? "" : " (".implode(", ", $attributesValues).")";
    }

    /**
     * Check if Attribute Array is Valid for Writing
     *
     * @param array $fieldData Attribute Array
     *
     * @return bool
     */
    public function isValidDefinition(array $fieldData): bool
    {
        //====================================================================//
        // Check Attribute is Array
        if (!is_iterable($fieldData) || empty($fieldData)) {
            return false;
        }
        //====================================================================//
        // Check Required Attributes Data are Given
        foreach (self::$requiredFields as $key => $name) {
            if (!isset($fieldData[$key])) {
                return Splash::log()->errTrace("Product ".$name." is Missing.");
            }
            if (empty($fieldData[$key]) || !is_scalar($fieldData[$key])) {
                return Splash::log()->errTrace("Product ".$name." is Missing.");
            }
        }

        return true;
    }

    /**
     * Load or Create Product Attribute Exists
     * Update Group Names in Extra Languages
     *
     * @param array|ArrayObject $attrItem Field Data
     *
     * @return ProductOptionInterface
     */
    public function touchProductOption($attrItem): ProductOptionInterface
    {
        //====================================================================//
        // Load Product Option
        $productOption = $this->options->findOneBy(array("code" => $attrItem["code"]));
        if ($productOption instanceof ProductOptionInterface) {
            return $productOption;
        }
        //====================================================================//
        // Create Product Option
        /** @var ProductOptionInterface $newOption */
        $newOption = $this->factory->createNew();
        //====================================================================//
        // Setup Option Code
        $newOption->setCode($attrItem["code"]);
        //====================================================================//
        // Persist Option
        $this->manager->persist($newOption);

        return $newOption;
    }

    /**
     * Update Product Option Names
     *
     * @param ProductOptionInterface $option
     * @param array|ArrayObject      $attrItem
     */
    public function updateProductOption(ProductOptionInterface &$option, $attrItem): void
    {
        //====================================================================//
        // Walk on All Available Languages
        foreach ($this->translations->getLocales() as $locale) {
            //====================================================================//
            // Decode Multilang Field Name
            $nameKey = "name".$this->translations->getLocaleSuffix($locale);
            //====================================================================//
            // Read New Attribute Name
            $newName = isset($attrItem[$nameKey]) ? $attrItem[$nameKey] : $attrItem["name"];
            //====================================================================//
            // Write Attribute Name
            $this->translations->setOptionTranslation($option, $locale, $newName);
        }
    }

    /**
     * Load or Create Product Attribute Value
     *
     * @param ProductOptionInterface $option
     * @param array|ArrayObject      $attrItem
     *
     * @return ProductOptionValueInterface
     */
    public function touchProductOptionValue(ProductOptionInterface $option, $attrItem): ProductOptionValueInterface
    {
        //====================================================================//
        // Walk on All Available Options Values
        foreach ($option->getValues() as $optionValue) {
            //====================================================================//
            // Compare Attribute Value Name
            if ($optionValue->getValue() == $attrItem["value"]) {
                return $optionValue;
            }
        }

        //====================================================================//
        // Create Product Option Value
        /** @var ProductOptionValueInterface $newValue */
        $newValue = $this->valuesFactory->createNew();
        //====================================================================//
        // Setup Option Value Name
        $newValue->setCode(strtolower(str_replace(' ', '_', $attrItem["value"])));
        $newValue->setValue($attrItem["value"]);
        //====================================================================//
        // Persist Option Value
        $this->manager->persist($newValue);
        //====================================================================//
        // Add Value to Option
        $option->addValue($newValue);

        return $newValue;
    }

    /**
     * Update Product Option Value Names
     *
     * @param ProductOptionValueInterface $value
     * @param array|ArrayObject           $attrItem
     */
    public function updateProductOptionValue(ProductOptionValueInterface &$value, $attrItem): void
    {
        //====================================================================//
        // Walk on All Available Languages
        foreach ($this->translations->getLocales() as $locale) {
            //====================================================================//
            // Decode Multilang Field Name
            $nameKey = "value".$this->translations->getLocaleSuffix($locale);
            //====================================================================//
            // Read New Attribute Value
            $newName = isset($attrItem[$nameKey]) ? $attrItem[$nameKey] : $attrItem["value"];
            //====================================================================//
            // Write Attribute Name
            $this->translations->setOptionValueTranslation($value, $locale, $newName);
        }
    }

    /**
     * Update Variant Option Value Names
     *
     * @param Variant                     $variant
     * @param ProductOptionValueInterface $newValue
     */
    public function updateVariantOptionValue(Variant &$variant, ProductOptionValueInterface $newValue): void
    {
        //====================================================================//
        // Walk on All Available Variant Options Values
        foreach ($variant->getOptionValues() as $optionValue) {
            //====================================================================//
            // Load Option
            $option = $optionValue->getOption();
            $newOption = $newValue->getOption();
            if (!$option || !$newOption) {
                continue;
            }
            //====================================================================//
            // Remove Similar Option Value
            if ($option->getCode() == $newOption->getCode()) {
                $variant->removeOptionValue($optionValue);
            }
        }
        //====================================================================//
        // Add Option Value to Variant
        $variant->addOptionValue($newValue);
    }

    /**
     * CleanUp Product Variant Attributes
     *
     * @param Variant $variant
     * @param array   $codes
     */
    public function cleanVariantOptionValues(Variant &$variant, array $codes): void
    {
        //====================================================================//
        // CleanUp Product Variant Attributes
        foreach ($variant->getOptionValues() as $optionValue) {
            //====================================================================//
            // Load Option
            $option = $optionValue->getOption();
            if (!$option) {
                continue;
            }
            //====================================================================//
            // Chezck if Option is Still Available
            if (!in_array($option->getCode(), $codes, true)) {
                $variant->removeOptionValue($optionValue);
            }
        }
    }
}

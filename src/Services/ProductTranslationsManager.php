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

namespace   Splash\Sylius\Services;

use Sylius\Component\Core\Model\ProductTranslationInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Locale\Model\LocaleInterface;
use Sylius\Component\Resource\Factory\Factory;

/**
 * Product Translations Manager
 * Manage Access to Products Translations
 */
class ProductTranslationsManager
{
    /**
     * @var Factory
     */
    protected $factory;

    /**
     * @var array
     */
    protected $config;

    /**
     * @var array
     */
    private $locales;

    /**
     * Service Constructor
     *
     * @param Factory $factory
     * @param array   $locales
     * @param array   $configuration
     */
    public function __construct(Factory $factory, array $locales, array $configuration)
    {
        //====================================================================//
        // Sylius Translations Factory
        $this->factory = $factory;
        //====================================================================//
        // Store List of Locales
        $this->locales = $locales;
        //====================================================================//
        // Store Bundle Configuration
        $this->config = $configuration;
    }

    /**
     * Get Array of Available Locale
     *
     * @param LocaleInterface $locale
     *
     * @return bool
     */
    public function getLocales(): array
    {
        return $this->locales;
    }

    /**
     * Check if Locale is Default Locale
     *
     * @param LocaleInterface $locale
     *
     * @return bool
     */
    public function isDefaultLocale(LocaleInterface $locale): bool
    {
        return ($locale->getCode() == $this->config["locale"]);
    }

    /**
     * Get Default Locale Iso Code
     *
     * @return string
     */
    public function getDefaultLocaleCode(): string
    {
        return $this->config["locale"];
    }

    /**
     * Decode Multilang FieldName with ISO Code
     *
     * @param LocaleInterface $locale    Sylius Locale Object
     * @param string          $fieldName Complete Field Name
     *
     * @return string Base Field Name or Empty String
     */
    public function fieldNameDecode(LocaleInterface $locale, $fieldName): string
    {
        //====================================================================//
        // Default Language => No code in FieldName
        if ($this->isDefaultLocale($locale)) {
            return $fieldName;
        }
        //====================================================================//
        // Other Languages => Check if Code is in FieldName
        if (false === strpos($fieldName, $locale->getCode())) {
            return "";
        }

        return substr($fieldName, 0, strlen($fieldName) - strlen($locale->getCode()) - 1);
    }

    /**
     * Get Translated Product String
     *
     * @param ProductVariantInterface $variant
     * @param LocaleInterface         $locale
     * @param type                    $baseFieldName
     *
     * @return string
     */
    public function getTranslated(ProductVariantInterface $variant, LocaleInterface $locale, $baseFieldName): string
    {
        $translations = $variant->getProduct()->getTranslations();

        if (isset($translations[$locale->getCode()])) {
            return (string) $translations[$locale->getCode()]->{ "get".ucfirst($baseFieldName) }();
        }

        return "";
    }

    /**
     * Set Translated Product String
     *
     * @param ProductVariantInterface $variant
     * @param LocaleInterface         $locale
     * @param string                  $baseFieldName
     * @param string                  $fieldData
     *
     * @return bool
     */
    public function setTranslated(ProductVariantInterface $variant, LocaleInterface $locale, string $baseFieldName, string $fieldData): bool
    {
        //====================================================================//
        // Load Product Translations
        $isoCode = $locale->getCode();
        $translations = $variant->getProduct()->getTranslations();
        //====================================================================//
        // Add Translation if No Exists
        if (!isset($translations[$isoCode])) {
            $translations[$isoCode] = $this->createTranslation($variant, $locale);
        }
        //====================================================================//
        // Compare Values
        $current = $translations[$locale->getCode()]->{ "get".ucfirst($baseFieldName) }();
        if ($current == $fieldData) {
            return false;
        }
        //====================================================================//
        // Write Value to Translation
        $translations[$isoCode]->{ "set".ucfirst($baseFieldName) }($fieldData);

        return true;
    }

    /**
     * Create New Product Translation
     *
     * @param ProductVariantInterface $variant
     * @param LocaleInterface         $locale
     *
     * @return ProductTranslationInterface
     */
    private function createTranslation(ProductVariantInterface $variant, LocaleInterface $locale): ProductTranslationInterface
    {
        //====================================================================//
        // Create New Translation
        $translation = $this->factory->createNew();
        $translation->setLocale($locale->getCode());
        $translation->setTranslatable($variant->getProduct());
        $translation->setName($variant->getCode());
        $translation->setSlug(uniqid($variant->getCode()));

        return $translation;
    }
}

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

namespace Splash\SyliusSplashPlugin\Objects\Product;

/**
 * Sylius Product Descriptions Fields
 */
trait DescriptionsTrait
{
    /**
     * List of System Available Locales
     *
     * @var array
     */
    protected array $availableLocales = array();

    /**
     * Build Fields using FieldFactory
     */
    public function buildDescriptionsFields(): void
    {
        $groupName = "Descriptions";
        $this->fieldsFactory()->setDefaultLanguage($this->translations->getDefaultLocaleCode());

        //====================================================================//
        // Walk on All Available Languages
        foreach ($this->translations->getLocales() as $locale) {
            //====================================================================//
            // Name without Options
            $this->fieldsFactory()->create(SPL_T_VARCHAR)
                ->identifier("name")
                ->name("Product Name without Options")
                ->group($groupName)
                ->microData("http://schema.org/Product", "alternateName")
                ->setMultilang($locale->getCode())
                ->isRequired()
            ;
            //====================================================================//
            // Name with Options
            $this->fieldsFactory()->create(SPL_T_VARCHAR)
                ->identifier("fullName")
                ->name("Product Name with Options")
                ->group($groupName)
                ->microData("http://schema.org/Product", "name")
                ->setMultilang($locale->getCode())
                ->isReadOnly()
            ;
            //====================================================================//
            // Slug
            $this->fieldsFactory()->create(SPL_T_VARCHAR)
                ->identifier("slug")
                ->name("Slug (Url)")
                ->group($groupName)
                ->microData("http://schema.org/Product", "urlRewrite")
                ->setMultilang($locale->getCode())
                ->association("name")
            ;
            //====================================================================//
            // Long Description
            $this->fieldsFactory()->create(SPL_T_TEXT)
                ->identifier("description")
                ->name("Description")
                ->group($groupName)
                ->microData("http://schema.org/Article", "articleBody")
                ->setMultilang($locale->getCode())
            ;
            //====================================================================//
            // Short Description
            $this->fieldsFactory()->create(SPL_T_VARCHAR)
                ->identifier("shortDescription")
                ->name("Short Description")
                ->group($groupName)
                ->microData("http://schema.org/Product", "description")
                ->setMultilang($locale->getCode())
            ;
            //====================================================================//
            // Meta Description
            $this->fieldsFactory()->create(SPL_T_VARCHAR)
                ->identifier("metaDescription")
                ->name("Meta Description")
                ->group($groupName)
                ->microData("http://schema.org/Article", "headline")
                ->setMultilang($locale->getCode())
            ;
        }
    }

    /**
     * Read requested Field
     *
     * @param string $key       Input List Key
     * @param string $fieldName Field Identifier / Name
     */
    public function getDescriptionsFields(string $key, string $fieldName): void
    {
        //====================================================================//
        // Walk on All Available Languages
        foreach ($this->translations->getLocales() as $locale) {
            //====================================================================//
            // Decode Multi-lang Field Name
            $baseFieldName = $this->translations->fieldNameDecode($locale, $fieldName);
            //====================================================================//
            // READ Fields
            switch ($baseFieldName) {
                //====================================================================//
                // Direct Readings
                case 'name':
                case 'slug':
                case 'description':
                case 'shortDescription':
                case 'metaDescription':
                    $this->out[$fieldName] = $this->translations
                        ->getTranslated($this->object, $locale, $baseFieldName);
                    unset($this->in[$key]);

                    break;
                case 'fullName':
                    // Read Variant Name
                    $this->out[$fieldName] = $this->translations
                        ->getTranslated($this->object, $locale, 'name');
                    // Complet Variant Name with Options Values
                    $this->out[$fieldName] .= $this->attributes
                        ->getOptionsNameString($this->object, $locale);

                    unset($this->in[$key]);

                    break;
            }
        }
    }

    /**
     * Write Given Fields
     *
     * @param string      $fieldName Field Identifier / Name
     * @param null|string $fieldData Field Data
     */
    public function setDescriptionsFields(string $fieldName, ?string $fieldData): void
    {
        //====================================================================//
        // Walk on All Available Languages
        foreach ($this->translations->getLocales() as $locale) {
            //====================================================================//
            // Decode Multi-lang Field Name
            $baseFieldName = $this->translations->fieldNameDecode($locale, $fieldName);
            //====================================================================//
            // READ Fields
            switch ($baseFieldName) {
                //====================================================================//
                // Direct Readings
                case 'name':
                case 'slug':
                case 'description':
                case 'shortDescription':
                case 'metaDescription':
                    $updated = $this->translations->setTranslated(
                        $this->object,
                        $locale,
                        $baseFieldName,
                        (string) $fieldData
                    );
                    unset($this->in[$fieldName]);

                    if ($updated) {
                        $this->needUpdate("product");
                    }

                    break;
            }
        }
    }
}

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

namespace Splash\Sylius\Objects\Product;

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
    protected $availableLocales = array();

    /**
     * Build Fields using FieldFactory
     */
    public function buildDescriptionsFields()
    {
        $groupName = "Descriptions";
        $this->fieldsFactory()->setDefaultLanguage("fr_FR");

        //====================================================================//
        // Walk on All Available Languages
        //====================================================================//
        // Name without Options
        $this->fieldsFactory()->create(SPL_T_VARCHAR)
            ->Identifier("name")
            ->Name("Product Name without Options")
            ->Group($groupName)
            ->MicroData("http://schema.org/Product", "alternateName")
            ->setDefaultLanguage('fr_FR')
            ->isRequired();

        //====================================================================//
        // Name with Options
        $this->fieldsFactory()->create(SPL_T_VARCHAR)
            ->Identifier("fullName")
            ->Name("Product Name with Options")
            ->Group($groupName)
            ->MicroData("http://schema.org/Product", "name")
            ->setDefaultLanguage('fr_FR')
            ->isReadOnly();

        //====================================================================//
        // Slug
        $this->fieldsFactory()->create(SPL_T_VARCHAR)
            ->Identifier("slug")
            ->Name("Slug (Url)")
            ->Group($groupName)
            ->MicroData("http://schema.org/Product", "urlRewrite")
            ->setDefaultLanguage('fr_FR')
            ->Association("name");

        //====================================================================//
        // Long Description
        $this->fieldsFactory()->create(SPL_T_TEXT)
            ->Identifier("description")
            ->Name("Description")
            ->Group($groupName)
            ->MicroData("http://schema.org/Article", "articleBody")
            ->setDefaultLanguage('fr_FR');

        //====================================================================//
        // Short Description
        $this->fieldsFactory()->create(SPL_T_VARCHAR)
            ->Identifier("shortDescription")
            ->Name("Short Description")
            ->Group($groupName)
            ->MicroData("http://schema.org/Product", "description")
            ->setDefaultLanguage('fr_FR');

        //====================================================================//
        // Meta Description
        $this->fieldsFactory()->create(SPL_T_VARCHAR)
            ->Identifier("metaDescription")
            ->Name("Meta Description")
            ->Group($groupName)
            ->MicroData("http://schema.org/Article", "headline")
            ->setDefaultLanguage('fr_FR');
    }

    /**
     * Read requested Field
     *
     * @param string $key Input List Key
     * @param string $fieldName Field Identifier / Name
     */
    public function getDescriptionsFields($key, $fieldName)
    {
        //====================================================================//
        // Walk on All Available Languages
        foreach ($this->translations->getLocales() as $locale) {
            if ($locale->getCode() == "fr_FR") {

                //====================================================================//
                // Decode Multilang Field Name
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
    }

    /**
     * Write Given Fields
     *
     * @param string $fieldName Field Identifier / Name
     * @param mixed $fieldData Field Data
     */
    public function setDescriptionsFields($fieldName, $fieldData)
    {
        //====================================================================//
        // Walk on All Available Languages
        foreach ($this->translations->getLocales() as $locale) {
            if ($locale->getCode() == "fr_FR") {
                //====================================================================//
                // Decode Multilang Field Name
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
                            $fieldData
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
}

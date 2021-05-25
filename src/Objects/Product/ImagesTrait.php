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

use Doctrine\ORM\PersistentCollection;
use Splash\Client\Splash;
use Sylius\Component\Core\Model\ImagesAwareInterface;
use Sylius\Component\Core\Model\ProductImageInterface;

/**
 * Sylius Product Images Fields
 */
trait ImagesTrait
{
    /**
     * Build Fields using FieldFactory
     */
    private function buildImagesFields()
    {
        $groupName = "Images";

        //====================================================================//
        // PRODUCT IMAGES
        //====================================================================//

        //====================================================================//
        // Product Images List
        $this->fieldsFactory()->create(SPL_T_IMG)
            ->Identifier("image")
            ->InList("images")
            ->Name("Image")
            ->Group($groupName)
            ->MicroData("http://schema.org/Product", "image");

        //====================================================================//
        // Product Images => Position
        $this->fieldsFactory()->create(SPL_T_INT)
            ->Identifier("position")
            ->InList("images")
            ->Name("Position")
            ->MicroData("http://schema.org/Product", "positionImage")
            ->Group($groupName)
            ->isReadOnly()
            ->isNotTested();

        //====================================================================//
        // Product Images => Is Cover
        $this->fieldsFactory()->create(SPL_T_BOOL)
            ->Identifier("cover")
            ->InList("images")
            ->Name("Cover")
            ->MicroData("http://schema.org/Product", "isCover")
            ->Group($groupName)
            ->isNotTested();

        //====================================================================//
        // Product Images => Is Visible Image
        $this->fieldsFactory()->create(SPL_T_BOOL)
            ->Identifier("visible")
            ->InList("images")
            ->Name("Visible")
            ->MicroData("http://schema.org/Product", "isVisibleImage")
            ->Group($groupName)
            ->isNotTested();
    }

    /**
     * Read requested Field
     *
     * @param string $key       Input List Key
     * @param string $fieldName Field Identifier / Name
     */
    private function getImagesFields($key, $fieldName)
    {
        //====================================================================//
        // Check if List field & Init List Array
        if (!($this->product instanceof ImagesAwareInterface)) {
            return;
        }
        //====================================================================//
        // Check if List field & Init List Array
        $fieldId = self::lists()->InitOutput($this->out, "images", $fieldName);
        if (!$fieldId) {
            return;
        }
        //====================================================================//
        // For All Availables Product Images
        $isFirst = true;
        foreach ($this->product->getImages() as $index => $image) {
            //====================================================================//
            // Safety Check for PhpStan
            if (!($image instanceof ProductImageInterface)) {
                continue;
            }
            //====================================================================//
            // Prepare
            switch ($fieldId) {
                case "image":
                    $value = $this->images->getImageField($image);

                    break;
                case "position":
                    $value = $index + 1;

                    break;
                case "visible":
                    $value = $this->images->isVisible($this->object, $image);

                    break;
                case "cover":
                    $value = $isFirst;

                    break;
                default:
                    return;
            }
            //====================================================================//
            // Insert Data in List
            self::lists()->Insert($this->out, "images", $fieldName, $index, $value);
            $isFirst = false;
        }
        unset($this->in[$key]);
    }

    /**
     * Write Given Fields
     *
     * @param string $fieldName Field Identifier / Name
     * @param mixed  $fieldData Field Data
     */
    private function setImagesFields($fieldName, $fieldData)
    {
        //====================================================================//
        // WRITE Field
        switch ($fieldName) {
            //====================================================================//
            // PRODUCT IMAGES
            //====================================================================//
            case 'images':
//                if(is_iterable($fieldData)) {
//                    $this->images->setImages($this->object, $fieldData);
//                    $images = $this->product->getImages();
//                    if (!($images instanceof PersistentCollection) || $images->isDirty()) {
                        $this->needUpdate("product");
//                    }
//                }

                break;
            default:
                return;
        }
        unset($this->in[$fieldName]);
    }
}

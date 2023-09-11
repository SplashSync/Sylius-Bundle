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

use Image;
use Sylius\Component\Core\Model\ProductImageInterface;

/**
 * Access to Product Cover Image Fields
 */
trait CoverImageTrait
{
    /**
     * Build Fields using FieldFactory
     *
     * @return void
     */
    protected function buildCoverImageFields(): void
    {
        //====================================================================//
        // Cover Image
        $this->fieldsFactory()->create(SPL_T_IMG, "cover_image")
            ->name("Cover Image")
            ->microData("http://schema.org/Product", "coverImage")
            ->isReadOnly()
        ;
        //====================================================================//
        // Cover Image Url
        $this->fieldsFactory()->create(SPL_T_URL, "cover_image_url")
            ->name("Cover Image Url")
            ->microData("http://schema.org/Product", "coverImageUrl")
            ->isReadOnly()
        ;
    }

    /**
     * Read requested Field
     *
     * @param string $key       Input List Key
     * @param string $fieldName Field Identifier / Name
     *
     * @return void
     */
    protected function getCoverImageFields(string $key, string $fieldName): void
    {
        //====================================================================//
        // READ Fields
        switch ($fieldName) {
            case 'cover_image':
                $this->out[$fieldName] = $this->getCoverImageDetails();

                break;
            case 'cover_image_url':
                $imgInfo = $this->getCoverImageDetails();
                $this->out[$fieldName] = $imgInfo ? $imgInfo['url'] : null;

                break;
            default:
                return;
        }

        unset($this->in[$key]);
    }

    /**
     * Get Product Cover Image Infos
     *
     * @return null|array
     */
    private function getCoverImageDetails(): ?array
    {
        //====================================================================//
        // For All Available Product Images
        foreach ($this->product->getImages() as $image) {
            //====================================================================//
            // Safety Check for PhpStan
            if (!($image instanceof ProductImageInterface)) {
                continue;
            }

            //====================================================================//
            // Prepare
            return $this->images->getImageField($image) ?: null;
        }

        return null;
    }
}

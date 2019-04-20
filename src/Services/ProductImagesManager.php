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

use ArrayObject;
use Doctrine\ORM\EntityManagerInterface as Manager;
use Doctrine\ORM\PersistentCollection;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Splash\Client\Splash;
use Splash\Models\Objects\ImagesTrait;
use Sylius\Component\Core\Model\ProductImageInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Resource\Factory\Factory;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface as Router;

/**
 * Product Images Manager
 * Manage Access to Products Images
 */
class ProductImagesManager
{
    use ImagesTrait;

    /**
     * @var Router
     */
    protected $router;

    /**
     * Doctrine Entity Manager
     *
     * @var Manager
     */
    protected $manager;

    /**
     * @var Factory
     */
    protected $factory;

    /**
     * @var CacheManager
     */
    protected $cache;

    /**
     * @var array
     */
    protected $config;

    /**
     * Product Current Images Collection
     *
     * @var PersistentCollection
     */
    private $currentImages;

    /**
     * Service Constructor
     *
     * @param Factory $factory
     * @param array   $locales
     * @param array   $configuration
     */
    public function __construct(Router $router, Manager $manager, Factory $factory, CacheManager $cache, array $configuration)
    {
        //====================================================================//
        // Symfony Router
        $this->router = $router;
        //====================================================================//
        // Sylius Images Manager
        $this->manager = $manager;
        //====================================================================//
        // Sylius Images Factory
        $this->factory = $factory;
        //====================================================================//
        // Liiop Images Cache Manager
        $this->cache = $cache;
        //====================================================================//
        // Store Bundle Configuration
        $this->config = $configuration;
    }

    //====================================================================//
    // PUBLIC METHODS
    //====================================================================//

    /**
     * Check if Image is Visible for this Product Variant
     *
     * @param ProductVariantInterface $variant
     * @param ProductImageInterface   $image
     *
     * @return bool
     */
    public function isVisible(ProductVariantInterface $variant, ProductImageInterface $image): bool
    {
        //====================================================================//
        // Check Image has Selected Variants
        if (0 == count($image->getProductVariants())) {
            return true;
        }

        //====================================================================//
        // Check Image is Visible in this Variant
        return $image->hasProductVariant($variant);
    }

    /**
     * Get Product Image Splash Field Data
     *
     * @param ProductImageInterface $image
     *
     * @return array
     */
    public function getImageField(ProductImageInterface $image)
    {
        //====================================================================//
        // Generate Images Base Path
        $imgPath = $this->config["images_folder"];
        //====================================================================//
        // Generate Public Url
        $publicUrl = $this->router->generate(
            "liip_imagine_filter",
            array("filter" => "sylius_large", "path" => $image->getPath()),
            UrlGeneratorInterface::ABSOLUTE_URL
        );
        //====================================================================//
        // Add Image
        return  self::images()->encode(
            $image->getType(),
            basename($image->getPath()),
            $imgPath.dirname($image->getPath())."/",
            $publicUrl
        );
    }

    /**
     * Set Product Images
     *
     * @param ProductVariantInterface $variant
     * @param ArrayObject             $fieldData
     *
     * @return bool
     */
    public function setImages(ProductVariantInterface $variant, ArrayObject $fieldData)
    {
        //====================================================================//
        // Get Current product Images Collection
        $this->currentImages = $variant->getProduct()->getImages();
        //====================================================================//
        // Init Images List Update
        $productImage = $this->currentImages->first();
        //====================================================================//
        // Given List Is Not Empty
        foreach ($fieldData as $inImage) {
            //====================================================================//
            // Safety Check: Image Array is here
            if (!isset($inImage["image"]) || empty($inImage["image"])) {
                continue;
            }

            //====================================================================//
            // No Image => Create A New One
            if (!($productImage instanceof ProductImageInterface)) {
                $productImage = $this->addImage($variant, $inImage["image"]);
            }
            //====================================================================//
            // Safety Check: We Should Have An Image Now
            if (!($productImage instanceof ProductImageInterface)) {
                continue;
            }
            //====================================================================//
            // Update Image File Contents
            $this->updateImage($productImage, $inImage["image"]);
            //====================================================================//
            // Update Image Visiblity for this Variant
            $this->updateVisibility($productImage, $variant, $inImage);
            //====================================================================//
            // Load Next Image
            $productImage = $this->currentImages->next();
        }

        //====================================================================//
        // Remove on List Remaining Items
        while ($productImage) {
            //====================================================================//
            // Remove Image
            $this->removeImage($productImage);
            //====================================================================//
            // Load Next Image
            $productImage = $this->currentImages->next();
        }

        return true;
    }

    //====================================================================//
    // PRODUCT IMAGES
    //====================================================================//

    /**
     * Update An Image with Image Field Data
     *
     * @param ProductImageInterface $productImage
     * @param ArrayObject           $inImage
     *
     * @return bool True is Image was Modified
     */
    public function updateImage(ProductImageInterface &$productImage, ArrayObject $inImage): bool
    {
        //====================================================================//
        // Check if Image Needs to Be Updated
        if ($this->isSearchedImage($productImage, $inImage["md5"])) {
            return false;
        }
        //====================================================================//
        // DownLoad Image from Splash Server
        $newImageFile = Splash::file()->getFile($inImage["file"], $inImage["md5"]);
        //====================================================================//
        // File Not Imported => Exit
        if (false == $newImageFile) {
            Splash::log()->war("Image Download Failed => Image Update Skipped");

            return false;
        }
        //====================================================================//
        // Get Images Base Path
        $basePath = $this->config["images_folder"];
        //====================================================================//
        // If Image Path is NOT Defined
        $imagePath = $productImage->getPath();
        if (empty($imagePath) || !is_file($basePath.$imagePath)) {
            //====================================================================//
            // Generate Image Encoded Path
            $imagePath = $this->generateRandomPath($inImage);
            //====================================================================//
            // Check if folder exists or create it
            self::createPath($basePath.$imagePath);
            //====================================================================//
            // Setup Image Path
            $productImage->setPath($imagePath);
        }
        //====================================================================//
        // Write Image On Folder
        Splash::file()->writeFile($basePath, $imagePath, $newImageFile["md5"], $newImageFile["raw"]);
        //====================================================================//
        // Refresh Liip Image Cache
        $this->cache->remove($imagePath);
        //====================================================================//
        // Setup Image Path
        $productImage->setPath($imagePath);
        Splash::log()->msg("New Image Loaded => ".$imagePath);

        return true;
    }

    /**
     * Update Image Variant Visibility
     *
     * @param ProductImageInterface   $productImage
     * @param ProductVariantInterface $variant
     * @param ArrayObject             $inImage
     *
     * @return bool True if Something Changed
     */
    private function updateVisibility(ProductImageInterface &$productImage, ProductVariantInterface $variant, ArrayObject $inImage): bool
    {
        //====================================================================//
        // Safety Check
        if (!isset($inImage["visible"])) {
            return false;
        }
        //====================================================================//
        // If Image Is Visible for this Variant
        if (!empty($inImage["visible"]) && !$productImage->hasProductVariant($variant)) {
            $productImage->addProductVariant($variant);

            return true;
        }
        //====================================================================//
        // If Image Is NOT Visible for this Variant
        if (empty($inImage["visible"]) && $productImage->hasProductVariant($variant)) {
            $productImage->removeProductVariant($variant);

            return true;
        }

        return false;
    }

    /**
     * Add An Image to Product
     *
     * @param ProductVariantInterface $variant
     * @param ArrayObject             $inImage
     *
     * @return ProductImageInterface
     */
    private function addImage(ProductVariantInterface $variant, ArrayObject $inImage): ProductImageInterface
    {
        //====================================================================//
        // Create a New Product Image
        $productImage = $this->factory->createNew();
        $this->manager->persist($productImage);
        //====================================================================//
        // Setup New Product Image
        $productImage->setOwner($variant->getProduct());
        if (isset($inImage["name"]) && !empty($inImage["name"])) {
            $productImage->setType($inImage["name"]);
        } elseif ($variant->getCode()) {
            $productImage->setType($variant->getCode()."-".uniqid());
        } else {
            $productImage->setType($variant->getProduct()->getCode()."-".uniqid());
        }
        //====================================================================//
        // Add to Product Images
        $this->currentImages->add($productImage);

        Splash::log()->war("Image Added to Collection");

        return $productImage;
    }

    /**
     * Remove the Image From Collection
     *
     * @param ProductImageInterface $productImage
     */
    private function removeImage(ProductImageInterface $productImage): void
    {
        //====================================================================//
        // Get Image File Path
        $imgPath = $this->config["images_folder"].$productImage->getPath();
        if (is_file($imgPath)) {
            //====================================================================//
            // Delete Product Image
            Splash::file()->deleteFile($imgPath, md5_file($imgPath));
        }
        //====================================================================//
        // Remove From Product Images
        $this->currentImages->remove($productImage);
        //====================================================================//
        // DeleteProduct Image
        $this->manager->remove($productImage);
        Splash::log()->msg("Image Object Deleted => ".$imgPath);
    }

    /**
     * Verify if is Searched Image
     *
     * @param ProductImageInterface $productImage Sylius Product Image
     * @param string                $inMd5        Expected Image Md5
     *
     * @retrurn     bool
     */
    private function isSearchedImage(ProductImageInterface $productImage, string $inMd5)
    {
        //====================================================================//
        // Read Image CheckSum
        $imgMd5 = md5_file($this->config["images_folder"].$productImage->getPath());
        //====================================================================//
        // If CheckSum are Similar => Unset & return Image
        return ($imgMd5 == $inMd5);
    }

    /**
     * Generate Image Encoded Path
     *
     * @param ArrayObject $inImage
     *
     * @return string
     */
    private function generateRandomPath(ArrayObject $inImage): string
    {
        //====================================================================//
        // Get Images Base Path
        $basePath = $this->config["images_folder"];
        //====================================================================//
        // Get Image File Extension
        $ext = pathinfo($inImage["file"], PATHINFO_EXTENSION);
        if (empty($ext) && !empty(pathinfo($inImage["filename"], PATHINFO_EXTENSION))) {
            $ext = pathinfo($inImage["filename"], PATHINFO_EXTENSION);
        }
        //====================================================================//
        // Generate Image Encoded Path
        do {
            // Generate a Unique Hash
            $hash = md5(uniqid(mt_rand(), true));
            // Expeand Image Path
            $imagePath = $this->expand($hash.'.'.$ext);
            // Check if File Already Exists
        } while (is_file($basePath.$imagePath));

        return $imagePath;
    }

    /**
     * Ensure Path Exists or Create Tt
     *
     * @param string $path
     *
     * @return string
     */
    private static function createPath(string $path): void
    {
        if (!is_dir(dirname(dirname($path)))) {
            mkdir(dirname(dirname($path)), 0775, true);
        }
        if (!is_dir(dirname($path))) {
            mkdir(dirname($path), 0775, true);
        }
    }

    /**
     * Expend File Hash to Path
     *
     * @param string $path
     *
     * @return string
     */
    private static function expand(string $path): string
    {
        return sprintf(
            '%s/%s/%s',
            substr($path, 0, 2),
            substr($path, 2, 2),
            substr($path, 4)
        );
    }
}

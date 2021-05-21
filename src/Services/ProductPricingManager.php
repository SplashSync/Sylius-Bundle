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

use Doctrine\ORM\EntityManagerInterface as Manager;
use Splash\Core\SplashCore      as Splash;
use Splash\Models\Objects\PricesTrait;
use Splash\Sylius\Helpers\ChannelsAwareTrait;
use Sylius\Bundle\ChannelBundle\Doctrine\ORM\ChannelRepository;
use Sylius\Component\Core\Model\ChannelInterface as Channel;
use Sylius\Component\Core\Model\ChannelPricingInterface as ChannelPricing;
use Sylius\Component\Core\Model\ProductVariantInterface as Variant;
use Sylius\Component\Resource\Factory\Factory;

/**
 * Product Pricing Manager
 * Manage Access to Products Variants Channels
 */
class ProductPricingManager
{
    use PricesTrait;
    use ChannelsAwareTrait;

    /**
     * Doctrine Entity Manager
     *
     * @var Manager
     */
    protected $entityManager;

    /**
     * @var Factory
     */
    protected $factory;

    /**
     * @var array
     */
    protected $config;

    /**
     * Service Constructor
     *
     * @param ChannelRepository $channels
     * @param Manager           $manager
     * @param Factory           $factory
     * @param array             $configuration
     */
    public function __construct(ChannelRepository $channels, Manager $manager, Factory $factory, array $configuration)
    {
        //====================================================================//
        // Sylius Channels Pricing manager
        $this->entityManager = $manager;
        //====================================================================//
        // Sylius Channel Pricing Factory
        $this->factory = $factory;
        //====================================================================//
        // Store Bundle Configuration
        $this->config = $configuration;
        //====================================================================//
        // Setup Sylius Channels Repository
        $this->setChannelsRepository($channels, $configuration);
    }

    /**
     * Get Product Price on a Channel
     *
     * @param Variant $variant
     * @param Channel $channel
     * @param bool    $original
     *
     * @return array|string
     */
    public function getChannelPrice(Variant $variant, Channel $channel, bool $original)
    {
        //====================================================================//
        // Retreive Price Currency
        $currency = $channel->getBaseCurrency();
        //====================================================================//
        // TODO : Select Default TaxZone in Parameters
        // Retreive Price TAX Percentile
        $taxCategory = $variant->getTaxCategory();
        $taxRate = $taxCategory
            ? $taxCategory->getRates()->first()->getAmount() * 100
            : 0.0;
        //====================================================================//
        // Identify Default Channel Price
        $price = 0;
        $channelPrice = $variant->getChannelPricingForChannel($channel);
        if ($channelPrice) {
            $price = $original ? $channelPrice->getOriginalPrice() : $channelPrice->getPrice();
        }
        //====================================================================//
        // Encode Splash Price Array
        return self::prices()->encode(
            doubleval($price / 100),        // No TAX Price
            $taxRate,                       // TAX Percent
            null,
            $currency ? (string) $currency->getCode() : "",
            $currency ? (string) $currency->getCode() : "",
            $currency ? (string) $currency->getName() : ""
        );
    }

    /**
     * Update Variant Channel Price
     *
     * @param Variant    $variant
     * @param Channel    $channel
     * @param bool       $original
     * @param null|array $fieldData
     *
     * @return bool
     */
    public function setChannelPrice(Variant $variant, Channel $channel, bool $original, $fieldData): bool
    {
        $updated = false;
        if (!is_iterable($fieldData) || !isset($fieldData["ttc"])) {
            return $updated;
        }
        //====================================================================//
        // Identify Default Channel Price
        $channelPrice = null;
        if ($variant->hasChannelPricingForChannel($channel)) {
            $channelPrice = $variant->getChannelPricingForChannel($channel);
        }
        //====================================================================//
        // Create Channel Price if Not Defined
        if (!$channelPrice) {
            //====================================================================//
            // Create New Channel Pricing from Factory
            /** @var ChannelPricing $channelPrice */
            $channelPrice = $this->factory->createNew();
            $channelPrice->setChannelCode($channel->getCode());
            $channelPrice->setProductVariant($variant);
            $channelPrice->setPrice(0);
            $channelPrice->setOriginalPrice(0);
            $variant->addChannelPricing($channelPrice);
            $this->entityManager->persist($channelPrice);
            $updated = true;
        }
        //====================================================================//
        // Init Channel Price
        $newPrice = (int) (round($fieldData["ttc"] * 100, 0, PHP_ROUND_HALF_UP));
        //====================================================================//
        // Get Current Price
        $currentPrice = $original ? $channelPrice->getOriginalPrice() : $channelPrice->getPrice();
        //====================================================================//
        // Compare Channel Price
        if ($newPrice == $currentPrice) {
            return $updated;
        }
        //====================================================================//
        // Update Product Price
        $original
            ? $channelPrice->setOriginalPrice($newPrice)
            : $channelPrice->setPrice($newPrice);

        return true;
    }
}

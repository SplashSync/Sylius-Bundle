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

use Doctrine\ORM\EntityManagerInterface as Manager;
use Exception;
use Splash\Models\Objects\PricesTrait;
use Splash\SyliusSplashPlugin\Helpers\ChannelsAwareTrait;
use Splash\SyliusSplashPlugin\Helpers\PriceBuilder;
use Sylius\Bundle\ChannelBundle\Doctrine\ORM\ChannelRepository;
use Sylius\Component\Core\Model\ChannelInterface as Channel;
use Sylius\Component\Core\Model\ChannelPricingInterface as ChannelPricing;
use Sylius\Component\Core\Model\ProductVariantInterface as Variant;
use Sylius\Component\Resource\Factory\Factory;
use Sylius\Component\Taxation\Model\TaxRateInterface;
use Sylius\Component\Taxation\Resolver\TaxRateResolverInterface;

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
    protected Manager $entityManager;

    /**
     * @var Factory
     */
    protected Factory $factory;

    /**
     * @var array
     */
    protected array $config;

    /**
     * @var TaxRateResolverInterface
     */
    private TaxRateResolverInterface $rateResolver;

    /**
     * @var TaxManager
     */
    private TaxManager $taxManager;

    /**
     * Product was Updated
     */
    private bool $updated = false;

    /**
     * Service Constructor
     */
    public function __construct(
        ChannelRepository $channels,
        Manager $manager,
        Factory $factory,
        array $configuration,
        TaxRateResolverInterface $rateResolver,
        TaxManager $taxManager
    ) {
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
        //====================================================================//
        // Store Sylius Rate Resolver
        $this->rateResolver = $rateResolver;
        //====================================================================//
        // Link to Splash Tax Manager
        $this->taxManager = $taxManager;
    }

    /**
     * Get Product Default Tax Rate for Channel
     */
    public function getDefaultRate(Variant $variant): ?TaxRateInterface
    {
        return $this->rateResolver->resolve($variant);
    }

    /**
     * Get Product Price on a Channel
     */
    public function getChannelPrice(Variant $variant, Channel $channel, bool $original): ?array
    {
        //====================================================================//
        // Retrieve Price Currency
        $currency = $channel->getBaseCurrency();
        //====================================================================//
        // Identify Default Channel Price
        $price = 0;
        $channelPrice = $variant->getChannelPricingForChannel($channel);
        if ($channelPrice) {
            $price = $original ? $channelPrice->getOriginalPrice() : $channelPrice->getPrice();
        }

        //====================================================================//
        // Encode Splash Price Array
        return PriceBuilder::toPrice(
            (int) $price,
            $currency ? (string) $currency->getCode() : "",
            $this->getDefaultRate($variant),
        );
    }

    /**
     * Update Variant Channel Price
     *
     * @throws Exception
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function setChannelPrice(Variant $variant, Channel $channel, bool $original, ?array $fieldData): bool
    {
        $this->updated = false;
        if (!is_iterable($fieldData) || !isset($fieldData["ht"])) {
            return false;
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
            $channelPrice = $this->createChannelPrice($variant, $channel);
            $this->updated = true;
        }
        //====================================================================//
        // Update Tax Category
        $taxRate = $this->updateTaxCategory($variant, $fieldData);
        //====================================================================//
        // Init Channel Price
        $newPrice = ($taxRate && $taxRate->isIncludedInPrice())
            ? (int) (round($fieldData["ttc"] * 100))
            : (int) (round($fieldData["ht"] * 100))
        ;
        //====================================================================//
        // Get Current Price
        $currentPrice = $original ? $channelPrice->getOriginalPrice() : $channelPrice->getPrice();
        //====================================================================//
        // Compare Channel Price
        if ($newPrice == $currentPrice) {
            return $this->updated;
        }
        //====================================================================//
        // Update Product Price
        $original
            ? $channelPrice->setOriginalPrice($newPrice)
            : $channelPrice->setPrice($newPrice)
        ;

        return true;
    }

    /**
     * Create a new Variant Channel Price
     */
    private function createChannelPrice(Variant $variant, Channel $channel): ChannelPricing
    {
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

        return $channelPrice;
    }

    /**
     * Get Product Closest Tax Rate for Default Channel
     *
     * @throws Exception
     */
    private function updateTaxCategory(Variant $variant, array $fieldData): ?TaxRateInterface
    {
        //====================================================================//
        // Identify Best Tax Rate
        $taxRate = $this->taxManager->getClosestTaxRate($variant, $fieldData["vat"] ?? null);
        //====================================================================//
        // No Tax Rate
        if (!$taxRate && !empty($variant->getTaxCategory())) {
            $variant->setTaxCategory(null);
            $this->updated = true;
        }
        //====================================================================//
        // Tax Rate Changed
        if ($taxRate && ($taxRate->getCategory() !== $variant->getTaxCategory())) {
            $variant->setTaxCategory($taxRate->getCategory());
            $this->updated = true;
        }

        return $taxRate;
    }
}

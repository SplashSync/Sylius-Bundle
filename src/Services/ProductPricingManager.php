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

use Sylius\Component\Core\Model\ProductVariantInterface as Variant;
use Sylius\Component\Resource\Factory\Factory;
use Doctrine\ORM\EntityManagerInterface as Manager;
use Sylius\Bundle\ChannelBundle\Doctrine\ORM\ChannelRepository;
use Sylius\Component\Core\Model\ChannelInterface as Channel;
use Splash\Models\Objects\PricesTrait;


/**
 * Product Pricing Manager
 * Manage Access to Products Variants Channels
 */
class ProductPricingManager
{
    use PricesTrait;
    
    /**
     * Doctrine Entity Manager
     *
     * @var Manager
     */
    protected $manager;

    /**
     * Doctrine Entity Manager
     *
     * @var ChannelRepository
     */
    protected $channels;
    
    /**
     * @var Factory
     */
    protected $factory;

    /**
     * @var array
     */
    protected $config;

    /**
     * @var ChannelInterface
     */
    private $defaultChannel;

    /**
     * Service Constructor
     *
     * @param Factory $factory
     * @param array   $locales
     * @param array   $configuration
     */
    public function __construct(ChannelRepository $channels, Manager $manager, Factory $factory, array $configuration)
    {
        //====================================================================//
        // Sylius Channels Repository
        $this->channels = $channels;
        //====================================================================//
        // Sylius Channels Pricing manager
        $this->manager = $manager;
        //====================================================================//
        // Sylius Translations Factory
        $this->factory = $factory;
        //====================================================================//
        // Store Bundle Configuration
        $this->config = $configuration;
        //====================================================================//
        // Detect Default Channel for Splash
        $channel = $channels->findOneByCode($configuration["default_channel"]);
        if(!($channel instanceof Channel)) {
            throw new Exception("Splash Bundle: Unable to Identify Default Sylius Channel");
        }
        $this->defaultChannel = $channel;
    }

    /**
     * Get Default Channel Code 
     *
     * @return string
     */
    public function getDefaultChannelCode(): string
    {
        return $this->defaultChannel->getCode();
    }

    /**
     * Get Default Channel Code 
     *
     * @return string
     */
    public function getDefaultChannel(): Channel
    {
        return $this->defaultChannel;
    }

    /**
     * Get All Available Channels 
     *
     * @return Channels[]
     */
    public function getChannels(): array
    {
        return $this->channels->findAll();
    }
    
    /**
     * Is Default Channel 
     *
     * @return bool
     */
    public function isDefaultChannel(Channel $channel): bool
    {
        if(!isset($this->defaultChannel)) {
            return false;
        }
        return ($this->defaultChannel->getCode() == $channel->getCode());
    }
    
    /**
     * Get Channel Suffix 
     *
     * @return string
     */
    public function getChannelSuffix(Channel $channel): string
    {
        if($this->isDefaultChannel($channel)) {
            return "";
        }
        return "_" . strtolower($channel->getCode());
    }    

    /**
     * Get Product Price on a Channel
     * @param Variant $variant
     * @param Channel $channel
     * @return false|array
     */
    public function getChannelPrice(Variant $variant, Channel $channel, bool $original)
    {
        //====================================================================//
        // Retreive Price Currency
        $currency       =   $channel->getBaseCurrency();
        //====================================================================//
        // TODO : Select Default TaxZone in Parameters
        // Retreive Price TAX Percentile
        if ($variant->getTaxCategory()) {
            $taxRate = $variant->getTaxCategory()->getRates()->first()->getAmount() * 100;
        } else {
            $taxRate = 0.0;
        }
        //====================================================================//
        // Identify Default Channel Price
        $price = 0;
        if($variant->hasChannelPricingForChannel($channel)) {
            $channelPrice   = $variant->getChannelPricingForChannel($channel);
            $price = $original ? $channelPrice->getOriginalPrice() : $channelPrice->getPrice();
        }
        
        //====================================================================//
        // Encode Splash Price Array
        return self::prices()->encode(
            doubleval($price / 100),        // No TAX Price
            $taxRate,                       // TAX Percent
            null,
            $currency->getCode(),
            $currency->getCode(),
            $currency->getName()
        );
    }
//    
//    public function getDefaultChannelPricing($Variant)
//    {
//        //====================================================================//
//        // Identify Default ChannelPricing
//        foreach ($Variant->getChannelPricings() as $ChannelPricing) {
//            $Code = method_exists($ChannelPricing, 'getChannel') ? $ChannelPricing->getChannel()->getCode() : $ChannelPricing->getChannelCode();
//            if ($Code == $this->parameters["default_channel"]) {
//                return $ChannelPricing;
//            }
//        }
//        //====================================================================//
//        // Create Channel Price if Needed
//        $ChannelPricing = new ChannelPricing();
//        $this->manager->persist($ChannelPricing);
//        //====================================================================//
//        // Identify Default ChannelPricing in Parameters
//        if (method_exists($ChannelPricing, 'setChannel')) {
//            $Channel = $this->channels->findOneByCode($this->parameters["default_channel"]);
//            if (!$Channel) {
//                $Channel = array_shift($this->channels->findAll());
//                Splash::Log()->Err("Sylius Default Channel Code Doesn't Exists!");
//            }
//            $ChannelPricing->setChannel($Channel);
//        } else {
//            if (!$this->channels->findOneByCode($this->parameters["default_channel"])) {
//                Splash::Log()->Err("Sylius Default Channel Code Doesn't Exists!");
//                $ChannelPricing->setChannelCode(array_shift($this->channels->findAll())->getCode());
//            } else {
//                $ChannelPricing->setChannelCode($this->parameters["default_channel"]);
//            }
//        }
//        $ChannelPricing->setProductVariant($Variant);
//        
//        //====================================================================//
//        // Add Channel Pricing to Variant
//        $Variant->getChannelPricings()->add($ChannelPricing);
//        //====================================================================//
//        // Return New Channel Pricing
//        return $ChannelPricing;
//    }
    
    /**
     * Update Variant Channel Price
     * @param Variant $variant
     * @param Channel $channel
     * @param bool $original
     * @param array $fieldData
     * @return bool
     */
    public function setChannelPrice(Variant $variant, Channel $channel, bool $original, array $fieldData): bool
    {
        if (!isset($fieldData["ht"])) {
            return false;
        }
        //====================================================================//
        // Identify Default Channel Price
        $currentPrice = 0;
        if($variant->hasChannelPricingForChannel($channel)) {
            $channelPrice   = $variant->getChannelPricingForChannel($channel);
            $currentPrice = $original ? $channelPrice->getOriginalPrice() : $channelPrice->getPrice();
        }
        //====================================================================//
        // Compare Channel Price
        if (!isset($fieldData["ht"])) {
            return false;
        }
        $ChannelPrice   = $this->getDefaultChannelPricing($variant);
        //====================================================================//
        // Update Product Price
        $ChannelPrice->setPrice($fieldData["ht"] * 100);
        return ;
    }
    
}

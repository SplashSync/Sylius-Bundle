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

namespace Splash\Sylius\Helpers;

use Exception;
use Sylius\Bundle\ChannelBundle\Doctrine\ORM\ChannelRepository;
use Sylius\Component\Core\Model\ChannelInterface;

/**
 * Give Access to Channels for Objects
 */
trait ChannelsAwareTrait
{
    /**
     * @var ChannelRepository
     */
    private $channels;

    /**
     * @var string
     */
    private $defaultChannelCode;
    
    
    /**
     * @var ChannelInterface
     */
    private $defaultChannel;
    
    /**
     * Setup Channels Repository
     *
     * @param ChannelRepository $channels
     * @param array   $configuration
     * 
     * @return $this
     */
    protected function setChannelsRepository(ChannelRepository $channels, array $configuration): self
    {
        //====================================================================//
        // Store link to Channels Repository
        $this->channels = $channels;
        //====================================================================//
        // Store Default Channels Code
        $this->defaultChannelCode = (string) $configuration["default_channel"];
        
        return $this;
    }
    
    /**
     * Get Default Channel Code 
     *
     * @return string
     */
    public function getDefaultChannelCode(): string
    {
        return (string) $this->defaultChannel->getCode();
    }

    /**
     * Get Default Channel Code 
     *
     * @return ChannelInterfaceing
     */
    public function getDefaultChannel(): ChannelInterface
    {
        if(!isset($this->defaultChannel)) {
            //====================================================================//
            // Detect Default Channel for Splash
            $channel = $this->channels->findOneByCode($this->defaultChannelCode);
            if(!($channel instanceof ChannelInterface)) {
                throw new Exception("Splash Bundle: Unable to Identify Default Sylius Channel");
            }
            //====================================================================//
            // Reload Channel from Entity Manager
            $defaultChannel = $this->entityManager->find(get_class($channel), $channel->getId());        
            if(!($defaultChannel instanceof ChannelInterface)) {
                throw new Exception("Splash Bundle: Unable to Identify Default Sylius Channel");
            }
            $this->defaultChannel = $defaultChannel;        
        }
        
        return $this->defaultChannel;
    }

    /**
     * Get All Available Channels 
     *
     * @return ChannelInterface[]
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
    public function isDefaultChannel(ChannelInterface $channel): bool
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
    public function getChannelSuffix(ChannelInterface $channel): string
    {
        if($this->isDefaultChannel($channel)) {
            return "";
        }
        return "_" . strtolower((string) $channel->getCode());
    }      
    
}

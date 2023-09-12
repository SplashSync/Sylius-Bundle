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

namespace Splash\SyliusSplashPlugin\Objects\Common;

/**
 * Access Channel Information on Orders & Invoices
 */
trait ChannelTrait
{
    /**
     * Build Fields using FieldFactory
     *
     * @return void
     */
    protected function buildChannelFields()
    {
        //====================================================================//
        // Sylius Channel Code
        $this->fieldsFactory()->create(SPL_T_VARCHAR)
            ->identifier("channel_code")
            ->name("Channel Code")
            ->group("Meta")
            ->microData("http://schema.org/Author", "alternateName")
            ->isReadOnly()
        ;
        //====================================================================//
        // Prestashop Shop Name
        $this->fieldsFactory()->create(SPL_T_VARCHAR)
            ->identifier("channel_name")
            ->name("Channel Name")
            ->group("Meta")
            ->microData("http://schema.org/Author", "name")
            ->isReadOnly()
        ;
        //====================================================================//
        // Prestashop Shop Url
        $this->fieldsFactory()->create(SPL_T_URL)
            ->identifier("channel_url")
            ->name("Channel Url")
            ->group("Meta")
            ->microData("http://schema.org/Author", "url")
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
    protected function getChannelFields(string $key, string $fieldName): void
    {
        //====================================================================//
        // READ Fields
        switch ($fieldName) {
            case 'channel_code':
                $channel = $this->object->getChannel();
                $this->out[$fieldName] = $channel ? $channel->getCode() : null;

                break;
            case 'channel_name':
                $channel = $this->object->getChannel();
                $this->out[$fieldName] = $channel ? $channel->getName() : null;

                break;
            case 'channel_url':
                $channel = $this->object->getChannel();
                $this->out[$fieldName] = $channel ? $channel->getHostname() : null;

                break;
            default:
                return;
        }

        unset($this->in[$key]);
    }
}

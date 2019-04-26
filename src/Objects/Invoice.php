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

namespace Splash\Sylius\Objects;

/**
 * Sylius Invoice Object
 *
 * @SuppressWarnings(PHPMD.CamelCasePropertyName)
 */
class Invoice extends Order
{
    //====================================================================//
    // Object Definition Parameters
    //====================================================================//

    /**
     *  Object Name (Translated by Module)
     */
    protected static $NAME = "Invoice";

    /**
     *  Object Description (Translated by Module).
     */
    protected static $DESCRIPTION = 'Sylius Invoice Object';

    /**
     *  Object Icon (FontAwesome or Glyph ico tag).
     */
    protected static $ICO = 'fa fa-money';

    /**
     *  Object Synchronistion Limitations
     *
     *  This Flags are Used by Splash Server to Prevent Unexpected Operations on Remote Server
     */
    // Allow Creation Of New Local Objects
    protected static $ALLOW_PUSH_CREATED = false;
    // Allow Update Of Existing Local Objects
    protected static $ALLOW_PUSH_UPDATED = false;
    // Allow Delete Of Existing Local Objects
    protected static $ALLOW_PUSH_DELETED = false;

    /**
     *  Object Synchronistion Recommended Configuration
     */
    // Enable Creation Of New Local Objects when Not Existing
    protected static $ENABLE_PUSH_CREATED = false;
    // Enable Update Of Existing Local Objects when Modified Remotly
    protected static $ENABLE_PUSH_UPDATED = false;
    // Enable Delete Of Existing Local Objects when Deleted Remotly
    protected static $ENABLE_PUSH_DELETED = false;
}

<?php

/*
 *  Copyright (C) BadPixxel <www.badpixxel.com>
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Splash\SyliusSplashPlugin\Objects;

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
     * {@inheritdoc}
     */
    protected static string $name = "Invoice";

    /**
     * {@inheritdoc}
     */
    protected static string $description = 'Sylius Invoice Object';

    /**
     * {@inheritdoc}
     */
    protected static string $ico = 'fa fa-money';

    //====================================================================//
    // Object Default Configuration
    //====================================================================//

    /**
     * {@inheritdoc}
     */
    protected static bool $allowPushCreated = false;

    /**
     * {@inheritdoc}
     */
    protected static bool $allowPushUpdated = false;

    /**
     * {@inheritdoc}
     */
    protected static bool $allowPushDeleted = false;

    /**
     * {@inheritdoc}
     */
    protected static bool $enablePushCreated = false;

    /**
     * {@inheritdoc}
     */
    protected static bool $enablePushUpdated = false;

    /**
     * {@inheritdoc}
     */
    protected static bool $enablePushDeleted = false;
}

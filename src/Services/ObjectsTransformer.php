<?php

/*
 * This file is part of SplashSync Project.
 *
 * Copyright (C) Splash Sync <www.splashsync.com>
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * @abstract    Sylius Bundle Data Transformer for Splash Bundle
 * @author      B. Paquier <contact@splashsync.com>
 */

namespace   Splash\Sylius\Services;

use Splash\Local\Objects\Transformer;

use Sylius\Component\Customer\Model\CustomerInterface;

/**
 * Description of ObjectTransformer
 *
 * @author nanard33
 */
class ObjectsTransformer extends Transformer
{
    public function __construct($Translator)
    {
        $this->translator = $Translator;
        
        return;
    }

    /**
     * @abstract Format Customer Address Province Code
     */
    public function getProvinceCode($Address)
    {
        if (null === $Address->getCountryCode()) {
            return $Address->getProvinceCode();
        }
        
        return substr($Address->getProvinceCode(), strlen($Address->getCountryCode()) + 1);
    }
}

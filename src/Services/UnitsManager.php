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

namespace Splash\SyliusSplashPlugin\Services;

use Splash\Components\UnitConverter;

class UnitsManager
{
    /**
     * Default Weights Unit
     *
     * @var string
     */
    private string $weightsUnit;

    /**
     * Default Dimensions Unit
     *
     * @var string
     */
    private string $dimensionsUnit;

    public function __construct(
        array $configuration
    ) {
        $this->weightsUnit = $configuration['units']['weights'] ?? "kg";
        $this->dimensionsUnit = $configuration['units']['dimensions'] ?? "m";
    }

    /**
     * Convert Weight form KiloGram to Target Unit
     */
    public function convertWeight(?float $weight): ?float
    {
        return $weight
            ? UnitConverter::convertWeight($weight, $this->getWeightFactor())
            : null
        ;
    }

    /**
     * Convert Weight from All Units to KiloGram
     */
    public function normalizeWeight(?float $weight): ?float
    {
        return $weight
            ? UnitConverter::normalizeWeight((float) $weight, $this->getWeightFactor())
            : null
        ;
    }

    /**
     * Convert Length form Meter to Target Unit
     */
    public function convertLength(?float $length): ?float
    {
        return $length
            ? UnitConverter::convertLength($length, $this->getLengthFactor())
            : null
        ;
    }

    /**
     * Convert Length from All Units to Meter
     */
    public function normalizeLength(?float $length): ?float
    {
        return $length
            ? UnitConverter::normalizeLength((float) $length, $this->getLengthFactor())
            : null
        ;
    }

    /**
     * Get Weight Factor for Weight Conversions
     */
    private function getWeightFactor(): float
    {
        switch ($this->weightsUnit) {
            case "g":
                return UnitConverter::MASS_GRAM;
            case "kg":
            default:
                return UnitConverter::MASS_KG;
            case "pound":
                return UnitConverter::MASS_LIVRE;
            case "once":
                return UnitConverter::MASS_OUNCE;
        }
    }

    /**
     * Get Length Factor for Dimensions Conversions
     */
    private function getLengthFactor(): float
    {
        switch ($this->dimensionsUnit) {
            case "mm":
                return UnitConverter::LENGTH_MILIMETER;
            case "cm":
                return UnitConverter::LENGTH_CENTIMETER;
            case "m":
            default:
                return UnitConverter::LENGTH_METER;
            case "inch":
                return UnitConverter::LENGTH_INCH;
            case "foot":
                return UnitConverter::LENGTH_FOOT;
        }
    }
}

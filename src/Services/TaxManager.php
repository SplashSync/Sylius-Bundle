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

use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Splash\Client\Splash;
use Splash\SyliusSplashPlugin\Helpers\ChannelsAwareTrait;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\AdjustmentInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ProductVariantInterface as Variant;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Component\Taxation\Model\TaxCategoryInterface;
use Sylius\Component\Taxation\Model\TaxRateInterface;
use Sylius\Component\Taxation\Resolver\TaxRateResolverInterface;

/**
 * Tooling Service for Working with Sylius Tax Rates
 */
class TaxManager
{
    use ChannelsAwareTrait;

    /**
     * Doctrine Entity Manager
     *
     * @var EntityManagerInterface
     */
    protected EntityManagerInterface $entityManager;

    /**
     * @var RepositoryInterface
     */
    private RepositoryInterface $taxRateRepository;

    /**
     * @var TaxRateResolverInterface
     */
    private TaxRateResolverInterface $rateResolver;

    public function __construct(
        array $configuration,
        ChannelRepositoryInterface $channels,
        RepositoryInterface $taxRateRepository,
        EntityManagerInterface $entityManager,
        TaxRateResolverInterface $rateResolver
    ) {
        //====================================================================//
        // Doctrine Entity Manager
        $this->entityManager = $entityManager;
        //====================================================================//
        // Setup Sylius Channels Repository
        $this->setChannelsRepository($channels, $configuration);
        $this->taxRateRepository = $taxRateRepository;
        $this->rateResolver = $rateResolver;
    }

    /**
     * Get Tax Rate for an Order Item
     *
     * @param AdjustmentInterface|object|OrderItemInterface $orderItem
     *
     * @return null|TaxRateInterface
     */
    public function getOrderItemTaxRate(object $orderItem): ?TaxRateInterface
    {
        $taxAdj = $taxRate = null;
        if ($orderItem instanceof OrderItemInterface) {
            $taxAdj = $orderItem->getAdjustmentsRecursively(AdjustmentInterface::TAX_ADJUSTMENT)->first();
        }
        if ($orderItem instanceof AdjustmentInterface) {
            $adjustable = $orderItem->getAdjustable();
            $taxAdj = $adjustable ? $adjustable->getAdjustments(AdjustmentInterface::TAX_ADJUSTMENT)->first() : null;
        }
        if ($taxAdj && ($taxRateCode = $taxAdj->getDetails()['taxRateCode'] ?? null)) {
            $taxRate = $this->taxRateRepository->findOneBy(array('code' => $taxRateCode));
        }

        return ($taxRate instanceof TaxRateInterface) ? $taxRate : null;
    }

    /**
     * Get Product Closest Tax Rate for Default Channel
     *
     * @throws Exception
     */
    public function getClosestTaxRate(Variant $variant, float $newRate): ?TaxRateInterface
    {
        //====================================================================//
        // Get Current Tax Rate
        $current = $this->rateResolver->resolve($variant);
        //====================================================================//
        // Current Tax Rate is Similar
        if ($current && (abs($newRate - $current->getAmountAsPercentage()) < 1E-3)) {
            return $current;
        }
        //====================================================================//
        // New Tax Rate is Empty
        if (!$newRate) {
            return null;
        }
        //====================================================================//
        // Search for Tax Rate in Channel Default Zone
        $closest = $this->getClosestTaxRateForDefaultZone($newRate);
        //====================================================================//
        // Not Found => Inform User
        if (!$closest) {
            Splash::log()->war(sprintf("Unable to identify Tax Rate %01.2f", $newRate));

            return $current;
        }

        return $closest;
    }

    /**
     * Get Product Closest Tax Category for Default Channel
     *
     * @throws Exception
     */
    public function getClosestTaxCategory(Variant $variant, float $newRate): ?TaxCategoryInterface
    {
        $taxRate = $this->getClosestTaxRate($variant, $newRate);

        return $taxRate ? $taxRate->getCategory() : null;
    }

    /**
     * Scan available Tax Rates for Default Zone and Find Closest Rate
     *
     * @throws Exception
     */
    private function getClosestTaxRateForDefaultZone(float $newRate): ?TaxRateInterface
    {
        //====================================================================//
        // Search for Tax Rate in Channel Default Zone
        $zone = $this->getDefaultChannel()->getDefaultTaxZone();
        /** @var TaxRateInterface[] $taxRates */
        $taxRates = $zone
            ? $this->taxRateRepository->findBy(array("zone" => $zone))
            : array()
        ;
        //====================================================================//
        // Walk on Tax Rate to find Closest
        $closest = null;
        foreach ($taxRates as $taxRate) {
            //====================================================================//
            // First Loop => Init Closest
            $closest ??= $taxRate;
            //====================================================================//
            // Next Loops => Compare Percentiles
            if (abs($taxRate->getAmountAsPercentage() - $newRate) < abs($closest->getAmountAsPercentage() - $newRate)) {
                $closest = $taxRate;
            }
        }
        //====================================================================//
        // Safety Check => Tax Rate is close to expected
        if ($closest && (abs($closest->getAmountAsPercentage() - $newRate) >= 0.5)) {
            $closest = null;
        }

        return $closest;
    }
}

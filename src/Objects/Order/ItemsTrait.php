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

namespace Splash\SyliusSplashPlugin\Objects\Order;

use Exception;
use Splash\SyliusSplashPlugin\Helpers\PriceBuilder;
use Sylius\Component\Core\Model\AdjustmentInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Product\Model\ProductTranslationInterface;

/**
 * Sylius Customer Order Items & Adjustment Field
 */
trait ItemsTrait
{
    /**
     * Build Fields using FieldFactory
     */
    protected function buildItemsFields(): void
    {
        //====================================================================//
        // Order Line Product SKU
        $this->fieldsFactory()->create(SPL_T_VARCHAR)
            ->identifier("sku")
            ->InList("items")
            ->name("All Items Sku")
            ->description("Sku of all items, including adjustments")
            ->microData("http://schema.org/Product", "sku")
            ->group("Products")
            ->isReadOnly()
        ;
        //====================================================================//
        // Order Line Product SKU
        $this->fieldsFactory()->create(SPL_T_VARCHAR)
            ->identifier("alternateSku")
            ->InList("items")
            ->name("Product Sku")
            ->description("Sku of all products, without adjustments")
            ->microData("http://schema.org/Product", "alternateName")
            ->group("Products")
            ->isReadOnly()
        ;
        //====================================================================//
        // Order Line Description
        $this->fieldsFactory()->create(SPL_T_VARCHAR)
            ->identifier("name")
            ->inList("items")
            ->name("Description")
            ->microData("http://schema.org/partOfInvoice", "description")
            ->group("Products")
            ->isReadOnly()
        ;
        //====================================================================//
        // Order Line Product Identifier
        $this->fieldsFactory()->create((string) self::objects()->encode("Product", SPL_T_ID))
            ->identifier("productId")
            ->inList("items")
            ->name("Product ID")
            ->microData("http://schema.org/Product", "productID")
            ->group("Products")
            ->isReadOnly()
        ;
        //====================================================================//
        // Order Line Quantity
        $this->fieldsFactory()->create(SPL_T_INT)
            ->identifier("qty")
            ->inList("items")
            ->name("Quantity")
            ->microData("http://schema.org/QuantitativeValue", "value")
            ->group("Products")
            ->isReadOnly()
        ;
        //====================================================================//
        // Order Line Unit Price
        $this->fieldsFactory()->create(SPL_T_PRICE)
            ->identifier("price")
            ->inList("items")
            ->name("Unit Price")
            ->microData("http://schema.org/PriceSpecification", "price")
            ->group("Products")
            ->isReadOnly()
        ;
        //====================================================================//
        // Order Line Tax Name
        $this->fieldsFactory()->create(SPL_T_VARCHAR)
            ->identifier("taxName")
            ->inList("items")
            ->name("Tax Name")
            ->microData("http://schema.org/PriceSpecification", "valueAddedTaxName")
            ->group("Products")
            ->isReadOnly()
        ;
        //====================================================================//
        // Order Line Discount
        $this->fieldsFactory()->create(SPL_T_DOUBLE)
            ->identifier("discount")
            ->inList("items")
            ->name("Discount (%)")
            ->microData("http://schema.org/Order", "discount")
            ->group("Products")
            ->isReadOnly()
        ;
    }

    /**
     * Read requested Field
     *
     * @param string $key       Input List Key
     * @param string $fieldName Field Identifier / Name
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    protected function getItemsFields(string $key, string $fieldName): void
    {
        //====================================================================//
        // Check if List field & Init List Array
        $fieldId = self::lists()->initOutput($this->out, "items", $fieldName);
        if (!$fieldId) {
            return;
        }
        //====================================================================//
        // Fill List with Data
        foreach ($this->getOrderItems() as $index => $orderItem) {
            //====================================================================//
            // READ Fields
            switch ($fieldId) {
                case 'sku':
                case 'alternateSku':
                case 'name':
                case 'productId':
                case 'qty':
                case 'price':
                case 'taxName':
                case 'discount':
                    $value = $this->{'getOrderItem'.ucfirst($fieldId)}($orderItem);

                    break;
                default:
                    return;
            }
            //====================================================================//
            // Insert Data in List
            self::lists()->insert($this->out, "items", $fieldName, $index, $value);
        }
        unset($this->in[$key]);
    }

    /**
     * Get Current Order Items
     *
     * @return array
     */
    private function getOrderItems(): array
    {
        //====================================================================//
        // Return Order Items
        return array_merge(
            $this->object->getItems()->toArray(),
            $this->object->getAdjustments(AdjustmentInterface::SHIPPING_ADJUSTMENT)->toArray(),
            $this->object->getAdjustments(AdjustmentInterface::ORDER_PROMOTION_ADJUSTMENT)->toArray(),
        );
    }

    /**
     * Get Order Item Sku
     *
     * @param AdjustmentInterface|object|OrderItemInterface $orderItem
     *
     * @throws Exception
     *
     * @return string
     */
    private function getOrderItemSku($orderItem): string
    {
        if ($orderItem instanceof OrderItemInterface) {
            return (string) $this->getOrderItemVariant($orderItem)->getCode();
        }
        if ($orderItem instanceof AdjustmentInterface) {
            return (string) $orderItem->getType();
        }

        return "";
    }

    /**
     * Get Order Items Only SKU, without adjustments SKUs
     *
     * @param AdjustmentInterface|object|OrderItemInterface $orderItem
     *
     * @throws Exception
     *
     * @return string
     */
    private function getOrderItemAlternateSku($orderItem): string
    {
        if ($orderItem instanceof OrderItemInterface) {
            return (string) $this->getOrderItemVariant($orderItem)->getCode();
        }

        return "";
    }

    /**
     * Get Order Item Name
     *
     * @param AdjustmentInterface|object|OrderItemInterface $orderItem
     *
     * @throws Exception
     *
     * @return string
     */
    private function getOrderItemName($orderItem): string
    {
        if ($orderItem instanceof OrderItemInterface) {
            $localCode = $this->getOrderItemOrder($orderItem)->getLocaleCode();
            /** @var null|ProductTranslationInterface $translation */
            $translation = $this->getOrderItemProduct($orderItem)
                ->getTranslations()
                ->get((string) $localCode)
            ;

            return $translation ? (string) $translation->getName() : "";
        }
        if ($orderItem instanceof AdjustmentInterface) {
            return (string) $orderItem->getLabel();
        }

        return "";
    }

    /**
     * Get Order Item Product Id
     *
     * @param AdjustmentInterface|OrderItemInterface $orderItem
     *
     * @throws Exception
     *
     * @return null|string
     */
    private function getOrderItemProductId($orderItem): ?string
    {
        if ($orderItem instanceof OrderItemInterface) {
            return self::objects()->encode(
                "Product",
                $this->getOrderItemVariant($orderItem)->getId()
            );
        }

        return null;
    }

    /**
     * Get Order Item Quantity
     *
     * @param AdjustmentInterface|object|OrderItemInterface $orderItem
     *
     * @return int
     */
    private function getOrderItemQty($orderItem): int
    {
        if ($orderItem instanceof OrderItemInterface) {
            return $orderItem->getQuantity();
        }
        if ($orderItem instanceof AdjustmentInterface) {
            return 1;
        }

        return 1;
    }

    /**
     * Get Order Item Unit Price
     *
     * @param AdjustmentInterface|OrderItemInterface $orderItem
     *
     * @throws Exception
     *
     * @return null|array
     */
    private function getOrderItemPrice($orderItem): ?array
    {
        $unitPrice = 0;
        if ($orderItem instanceof OrderItemInterface) {
            $unitPrice = $orderItem->getUnitPrice();
        }
        if ($orderItem instanceof AdjustmentInterface) {
            $unitPrice = $orderItem->getAmount();
        }

        //====================================================================//
        // Encode Splash Price Array
        return PriceBuilder::toPrice(
            $unitPrice,
            (string) $this->object->getCurrencyCode(),
            $this->taxManager->getOrderItemTaxRate($orderItem),
        );
    }

    /**
     * Get Order Item Unit Price
     *
     * @param AdjustmentInterface|OrderItemInterface $orderItem
     *
     * @throws Exception
     *
     * @return null|string
     */
    private function getOrderItemTaxName($orderItem): ?string
    {
        $taxRate = $this->taxManager->getOrderItemTaxRate($orderItem);
        if ($taxRate) {
            return $taxRate->getCode();
        }

        return null;
    }

    /**
     * Get Order Item Discount Percentile
     *
     * @param AdjustmentInterface|OrderItemInterface $orderItem
     *
     * @return float
     */
    private function getOrderItemDiscount($orderItem): float
    {
        $unitPrice = 0.0;
        $adjustable = null;
        if ($orderItem instanceof OrderItemInterface) {
            $unitPrice = $orderItem->getUnitPrice();
            $adjustable = $orderItem->getUnits()->first();
        }
        if ($orderItem instanceof AdjustmentInterface) {
            $unitPrice = $orderItem->getAmount();
            $adjustable = $orderItem->getAdjustable();
        }
        if (!$adjustable || empty($unitPrice)) {
            return 0.0;
        }

        $discount = $adjustable->getAdjustmentsTotal(AdjustmentInterface::ORDER_ITEM_PROMOTION_ADJUSTMENT);
        $discount += $adjustable->getAdjustmentsTotal(AdjustmentInterface::ORDER_PROMOTION_ADJUSTMENT);
        $discount += $adjustable->getAdjustmentsTotal(AdjustmentInterface::ORDER_SHIPPING_PROMOTION_ADJUSTMENT);
        $discount += $adjustable->getAdjustmentsTotal(AdjustmentInterface::ORDER_UNIT_PROMOTION_ADJUSTMENT);

        return doubleval(round(-100 * $discount / $unitPrice, 1, PHP_ROUND_HALF_UP));
    }

    /**
     * Get Order Item Order
     *
     * @param OrderItemInterface $orderItem
     *
     * @return OrderInterface
     */
    private function getOrderItemOrder(OrderItemInterface $orderItem): OrderInterface
    {
        $order = $orderItem->getOrder();
        if (!($order instanceof OrderInterface)) {
            throw new Exception("Unable to Load Oder Item Order");
        }

        return $order;
    }

    /**
     * Get Order Item Product
     *
     * @param OrderItemInterface $orderItem
     *
     * @return ProductInterface
     */
    private function getOrderItemProduct(OrderItemInterface $orderItem): ProductInterface
    {
        $product = $this->getOrderItemVariant($orderItem)->getProduct();
        if (!($product instanceof ProductInterface)) {
            throw new Exception("Unable to Load Oder Item Product");
        }

        return $product;
    }

    /**
     * Get Order Item Variant
     *
     * @param OrderItemInterface $orderItem
     *
     * @return ProductVariantInterface
     */
    private function getOrderItemVariant(OrderItemInterface $orderItem): ProductVariantInterface
    {
        $variant = $orderItem->getVariant();
        if (!($variant instanceof ProductVariantInterface)) {
            throw new Exception("Unable to Load Oder Item Product Variant");
        }

        return $variant;
    }
}

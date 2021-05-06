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

namespace Splash\Sylius\Objects\Order;

use Sylius\Component\Core\Model\AdjustmentInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Order\Model\Adjustment;

/**
 * Sylius Customer Order Items & Adjustements Field
 */
trait ItemsTrait
{
    /**
     * Build Fields using FieldFactory
     */
    protected function buildItemsFields()
    {
        //====================================================================//
        // Order Line Name
        $this->fieldsFactory()->create(SPL_T_VARCHAR)
            ->Identifier("sku")
            ->InList("items")
            ->Name("Product Sku")
            ->MicroData("http://schema.org/partOfInvoice", "name")
            ->Group("Products")
            ->isReadOnly();

        //====================================================================//
        // Order Line Description
        $this->fieldsFactory()->create(SPL_T_VARCHAR)
            ->Identifier("name")
            ->InList("items")
            ->Name("Description")
            ->MicroData("http://schema.org/partOfInvoice", "description")
            ->Group("Products")
            ->isReadOnly();

        //====================================================================//
        // Order Line Product Identifier
        $this->fieldsFactory()->create(self::objects()->Encode("Product", SPL_T_ID))
            ->Identifier("productId")
            ->InList("items")
            ->Name("Product ID")
            ->MicroData("http://schema.org/Product", "productID")
            ->Group("Products")
            ->isReadOnly();

        //====================================================================//
        // Order Line Quantity
        $this->fieldsFactory()->create(SPL_T_INT)
            ->Identifier("qty")
            ->InList("items")
            ->Name("Quantity")
            ->MicroData("http://schema.org/QuantitativeValue", "value")
            ->Group("Products")
            ->isReadOnly();

        //====================================================================//
        // Order Line Unit Price
        $this->fieldsFactory()->create(SPL_T_PRICE)
            ->Identifier("price")
            ->InList("items")
            ->Name("Unit Price")
            ->MicroData("http://schema.org/PriceSpecification", "price")
            ->Group("Products")
            ->isReadOnly();

        //====================================================================//
        // Order Line Discount
        $this->fieldsFactory()->create(SPL_T_DOUBLE)
            ->Identifier("discount")
            ->InList("items")
            ->Name("Discount (%)")
            ->MicroData("http://schema.org/Order", "discount")
            ->Group("Products")
            ->isReadOnly();
    }

    /**
     * Read requested Field
     *
     * @param string $key       Input List Key
     * @param string $fieldName Field Identifier / Name
     */
    protected function getItemsFields($key, $fieldName)
    {
        //====================================================================//
        // Check if List field & Init List Array
        $fieldId = self::lists()->InitOutput($this->out, "items", $fieldName);
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
                case 'name':
                case 'productId':
                case 'qty':
                case 'price':
                case 'discount':
                    $value = $this->{'getOrderItem'.ucfirst($fieldId)}($orderItem);

                    break;
                default:
                    return;
            }
            //====================================================================//
            // Insert Data in List
            self::lists()->Insert($this->out, "items", $fieldName, $index, $value);
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
            $this->object->getAdjustments()->toArray()
        );
    }

    /**
     * Get Order Item Sku
     *
     * @param Adjustment|OrderItemInterface $orderItem
     *
     * @return string
     */
    private function getOrderItemSku($orderItem): string
    {
        if ($orderItem instanceof OrderItemInterface) {
            return (string) $this->getOrderItemVariant($orderItem)->getCode();
        }
        if ($orderItem instanceof Adjustment) {
            return (string) $orderItem->getType();
        }

        return "";
    }

    /**
     * Get Order Item Name
     *
     * @param Adjustment|OrderItemInterface $orderItem
     *
     * @return string
     */
    private function getOrderItemName($orderItem): string
    {
        if ($orderItem instanceof OrderItemInterface) {
            $localCode = $this->getOrderItemOrder($orderItem)->getLocaleCode();

            return $this->getOrderItemProduct($orderItem)
                ->getTranslations()->get((string) $localCode)
                ->getName();
        }
        if ($orderItem instanceof Adjustment) {
            return (string) $orderItem->getLabel();
        }

        return "";
    }

    /**
     * Get Order Item Product Id
     *
     * @param Adjustment|OrderItemInterface $orderItem
     *
     * @return null|string
     */
    private function getOrderItemProductId($orderItem)
    {
        if ($orderItem instanceof OrderItemInterface) {
            return self::objects()->encode(
                "Product",
                $this->getOrderItemProduct($orderItem)->getId()
            );
        }

        return null;
    }

    /**
     * Get Order Item Quantity
     *
     * @param Adjustment|OrderItemInterface $orderItem
     *
     * @return int
     */
    private function getOrderItemQty($orderItem): int
    {
        if ($orderItem instanceof OrderItemInterface) {
            return $orderItem->getQuantity();
        }
        if ($orderItem instanceof Adjustment) {
            return 1;
        }
    }

    /**
     * Get Order Item Quantity
     *
     * @param Adjustment|OrderItemInterface $orderItem
     *
     * @return array|string
     */
    private function getOrderItemPrice($orderItem)
    {
        $taxRate = 0.0;
        $unitPrice = 0.0;
        if ($orderItem instanceof OrderItemInterface) {
            $taxCategory = $this->getOrderItemVariant($orderItem)->getTaxCategory();
            if ($taxCategory) {
                $taxRate = $taxCategory->getRates()->first()->getAmount() * 100;
            }
            $unitPrice = $orderItem->getUnitPrice();
            $unitPrice = $unitPrice - (($unitPrice/(100+$taxRate))*$taxRate);
        }
        if ($orderItem instanceof Adjustment) {
            $unitPrice = $orderItem->getAmount();
        }

        //====================================================================//
        // Encode Splash Price Array
        return self::prices()->encode(
            doubleval($unitPrice / 100),            // No TAX Price
            $taxRate,                               // TAX Percent
            null,
            (string) $this->object->getCurrencyCode(),
            (string) $this->object->getCurrencyCode(),
            (string) $this->object->getCurrencyCode()
        );
    }

    /**
     * Get Order Item Quantity
     *
     * @param Adjustment|OrderItemInterface $orderItem
     *
     * @return float
     */
    private function getOrderItemDiscount($orderItem): float
    {
        if (!($orderItem instanceof OrderItemInterface)) {
            return 0.0;
        }
        if ($orderItem->getUnits()->isEmpty()) {
            return 0.0;
        }

        $firtsUnit = $orderItem->getUnits()->first();
        $discount = $firtsUnit->getAdjustmentsTotal(AdjustmentInterface::ORDER_ITEM_PROMOTION_ADJUSTMENT);
        $discount += $firtsUnit->getAdjustmentsTotal(AdjustmentInterface::ORDER_PROMOTION_ADJUSTMENT);
        $discount += $firtsUnit->getAdjustmentsTotal(AdjustmentInterface::ORDER_SHIPPING_PROMOTION_ADJUSTMENT);
        $discount += $firtsUnit->getAdjustmentsTotal(AdjustmentInterface::ORDER_UNIT_PROMOTION_ADJUSTMENT);

        return doubleval(round(-100 * $discount / $orderItem->getUnitPrice(), 1, PHP_ROUND_HALF_UP));
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
            throw new \Exception("Unable to Load Oder Item Order");
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
            throw new \Exception("Unable to Load Oder Item Product");
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
            throw new \Exception("Unable to Load Oder Item Product Variant");
        }

        return $variant;
    }
}

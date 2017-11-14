<?php

namespace Splash\Sylius\Objects\Traits;

use Splash\Bundle\Annotation as SPL;

use Splash\Models\ObjectBase;
use Sylius\Component\Core\Model\OrderItem;
use Sylius\Component\Order\Model\Adjustment;
use Sylius\Component\Core\Model\AdjustmentInterface;

trait OrderItemsTrait {

    public function getItems($Order)
    {
        //====================================================================//
        // Fetch Order Commons Infos
        $this->Order        = $Order;
        $this->Currency     = $Order->getChannel()->getBaseCurrency();        
        //====================================================================//
        // Return Order Items
        return array_merge (
                $Order->getItems()->toArray(),
                $Order->getAdjustments()->toArray()
                );
    }
    
    /**
     * @SPL\Field(  
     *          id      =   "sku@items",
     *          type    =   "varchar@list",
     *          name    =   "Sku",
     *          itemtype=   "http://schema.org/partOfInvoice", itemprop="name",
     *          write   = false,
     * )
     */
    protected $ItemsSku;
    
    public function getSku($OrderItem)
    {
        if ( $OrderItem instanceof OrderItem) {
            return $OrderItem->getVariant()->getCode();
        } elseif ( $OrderItem instanceof Adjustment) {
            return $OrderItem->getType();
        }
    }
    
    
    /**
     * @SPL\Field(  
     *          id      =   "name@items",
     *          type    =   "varchar@list",
     *          name    =   "Name",
     *          itemtype=   "http://schema.org/partOfInvoice", itemprop="description",
     *          write   = false,
     * )
     */
    protected $ItemsName;    
    
    public function getName($OrderItem)
    {
        if ( $OrderItem instanceof OrderItem) {
            $LocalCode = $OrderItem->getOrder()->getLocaleCode();
            return $OrderItem->getVariant()->getProduct()
                    ->getTranslations()->get($LocalCode)
                    ->getName();
        } elseif ( $OrderItem instanceof Adjustment) {
            return $OrderItem->getLabel();
        }
    }
    
    
    /**
     * @SPL\Field(  
     *          id      =   "product@items",
     *          type    =   "objectid::Product@list",
     *          name    =   "Product",
     *          itemtype=   "http://schema.org/Product", itemprop="productID",
     *          write   = false,
     * )
     */
    protected $ItemsProductId; 
    
    public function getProduct($OrderItem)
    {
        if ( $OrderItem instanceof OrderItem) {
            return $OrderItem->getVariant();
        } elseif ( $OrderItem instanceof Adjustment) {
            return Null;
        }
    }
    
    /**
     * @SPL\Field(  
     *          id      =   "qty@items",
     *          type    =   "int@list",
     *          name    =   "Quantity",
     *          itemtype=   "http://schema.org/QuantitativeValue", itemprop="value",
     *          write   = false,
     * )
     */
    protected $ItemsQty; 
    
    public function getQty($OrderItem)
    {
        if ( $OrderItem instanceof OrderItem) {
            return $OrderItem->getQuantity();
        } elseif ( $OrderItem instanceof Adjustment) {
            return 1;
        }
    }
    
    /**
     * @SPL\Field(  
     *          id      =   "price@items",
     *          type    =   "price@list",
     *          name    =   "Price",
     *          itemtype=   "http://schema.org/PriceSpecification", itemprop="price",
     *          write   = false,
     * )
     */
    protected $ItemsUnitPrice; 
    
    public function getPrice($OrderItem)
    {
        $TaxRate    = 0.0;
        $UnitPrice  = 0.0;
        if ( $OrderItem instanceof OrderItem) {
            
            if ($OrderItem->getVariant()->getTaxCategory()) {
                $TaxRate = $OrderItem->getVariant()->getTaxCategory()->getRates()->first()->getAmount() * 100;
            }
            $UnitPrice  = $OrderItem->getUnitPrice();

        } elseif ( $OrderItem instanceof Adjustment) {
            
            $UnitPrice  = $OrderItem->getAmount();
            
        }
        
        return ObjectBase::Price_Encode(
                doubleval($UnitPrice / 100),        // No TAX Price 
                $TaxRate,                           // TAX Percent
                Null, 
                $this->Currency->getCode(),
                $this->Currency->getCode(),
                $this->Currency->getName());
    }  
    
    /**
     * @SPL\Field(  
     *          id      =   "discount@items",
     *          type    =   "double@list",
     *          name    =   "Discount %",
     *          itemtype=   "http://schema.org/Order", itemprop="discount",
     *          write   = false,
     * )
     */
    protected $ItemsDiscount; 
    
    public function getDiscount($OrderItem)
    {
        $DiscountPercent = 0;
        if ( $OrderItem instanceof OrderItem) {
            
            if ($OrderItem->getUnits()->isEmpty()) {
                return 0;
            }

            $FirtsUnit = $OrderItem->getUnits()->first();
            $Discount  = $FirtsUnit->getAdjustmentsTotal(AdjustmentInterface::ORDER_ITEM_PROMOTION_ADJUSTMENT);
            $Discount += $FirtsUnit->getAdjustmentsTotal(AdjustmentInterface::ORDER_PROMOTION_ADJUSTMENT);
            $Discount += $FirtsUnit->getAdjustmentsTotal(AdjustmentInterface::ORDER_SHIPPING_PROMOTION_ADJUSTMENT);
            $Discount += $FirtsUnit->getAdjustmentsTotal(AdjustmentInterface::ORDER_UNIT_PROMOTION_ADJUSTMENT);
            
            
            $DiscountPercent = doubleval( round ( -100 * $Discount / $OrderItem->getUnitPrice(), 1));
            
        }
        
        return $DiscountPercent;
    }
    
}

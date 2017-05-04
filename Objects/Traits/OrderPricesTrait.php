<?php

namespace Splash\Sylius\Objects\Traits;

use Splash\Bundle\Annotation as SPL;

trait OrderPricesTrait {
    
    /**
     * @SPL\Field(  
     *          id      =   "currencyCode",
     *          type    =   "currency",
     *          name    =   "Currency Code",
     *          itemtype=   "hhttps://schema.org/PriceSpecification", itemprop="priceCurrency",
     *          required=   true,
     * )
     */
    protected $currencyCode;  
    
    /**
     * @SPL\Field(  
     *          id      =   "total",
     *          type    =   "double",
     *          name    =   "Total",
     *          itemtype=   "http://schema.org/Invoice", itemprop="totalPaymentDueTaxIncluded",
     *          inlist  =   true,
     *          write   =   false,
     * )
     */
    protected $total;   
    
    public function getTotal($Order)
    {
        return doubleval($Order->getTotal() / 100);
    }        
    
}

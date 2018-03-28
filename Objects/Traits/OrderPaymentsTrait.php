<?php

namespace Splash\Sylius\Objects\Traits;

use Splash\Bundle\Annotation as SPL;

use Sylius\Component\Payment\Model\PaymentInterface;

trait OrderPaymentsTrait
{
    public function getPayments($Order)
    {
        $CompletedStates = array(
//            PaymentInterface::STATE_AUTHORIZED,
            PaymentInterface::STATE_COMPLETED,
        );
        //====================================================================//
        // Fetch Order Commons Infos
        $this->Order        = $Order;
        $this->Currency     = $Order->getChannel()->getBaseCurrency();
        //====================================================================//
        // Fetch Order Completed Payments
        $CompletedPayments = array();
        foreach ($Order->getPayments() as $Payment) {
            if (!in_array($Payment->getState(), $CompletedStates)) {
                continue;
            }
            $CompletedPayments[]    =   $Payment;
        }
        //====================================================================//
        // Return Completed Order Payments
        return $CompletedPayments;
    }
    
    /**
     * @SPL\Field(
     *          id      =   "createdAt@payments",
     *          type    =   "date@list",
     *          name    =   "Payment Date",
     *          itemtype=   "http://schema.org/PaymentChargeSpecification", itemprop="validFrom",
     *          write   =   false,
     * )
     */
    protected $paymentsDate;
 
     
    /**
     * @SPL\Field(
     *          id      =   "id@payments",
     *          type    =   "varchar@list",
     *          name    =   "Payment Number",
     *          itemtype=   "http://schema.org/Invoice", itemprop="paymentMethodId",
     *          write   =   false,
     * )
     */
    protected $paymentsNumber;
    
    /**
     * @SPL\Field(
     *          id      =   "method@payments",
     *          type    =   "varchar@list",
     *          name    =   "Payment Method",
     *          itemtype=   "http://schema.org/Invoice", itemprop="paymentMethodId",
     *          write   =   false,
     * )
     */
    protected $paymentsMethod;
    
    
    public function getMethod(PaymentInterface $Payment)
    {
        if ($Payment->getMethod()) {
            return $Payment->getMethod()->getCode();
        }
        return null;
    }
    
    /**
     * @SPL\Field(
     *          id      =   "amount@payments",
     *          type    =   "double@list",
     *          name    =   "Payment Amount",
     *          itemtype=   "http://schema.org/PaymentChargeSpecification", itemprop="price",
     *          write   =   false,
     * )
     */
    protected $paymentsAmount;
    
    public function getAmount(PaymentInterface $Payment)
    {
        return doubleval($Payment->getAmount() / 100);
    }
    
    /**
     * @SPL\Field(
     *          id      =   "currencyCode@payments",
     *          type    =   "currency@list",
     *          name    =   "Payment Currency",
     *          itemtype=   "http://schema.org/PaymentChargeSpecification", itemprop="priceCurrency",
     *          write   =   false,
     * )
     */
    protected $paymentsCurrency;
}

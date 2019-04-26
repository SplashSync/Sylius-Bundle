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

use Sylius\Component\Payment\Model\PaymentInterface;

/**
 * Sylius Customer Order Payments Field
 */
trait PaymentsTrait
{
    /**
     * Current Listed Sylius Payment
     *
     * @var PaymentInterface
     */
    protected $payment;
    
    /**
     * Known Payment Method Codes Names
     *
     * @var array
     */
    protected static $knownMethods = array(
        "cash_on_delivery" => "COD",

        "bank_transfer" => "ByBankTransferInAdvance",
        "offline" => "ByBankTransferInAdvance",

        "paypal" => "PayPal",
        "paypal_express_checkout" => "PayPal",

        "stripe" => "DirectDebit",
        "stripe_checkout" => "DirectDebit",
    );

    /**
     * List of Completed Payments States
     *
     * @var array
     */
    protected static $completedStates = array(
        PaymentInterface::STATE_AUTHORIZED,
        PaymentInterface::STATE_COMPLETED,
    );

    /**
     * Build Fields using FieldFactory
     */
    protected function buildPaymentsFields()
    {
        //====================================================================//
        // Payment Line Date
        $this->fieldsFactory()->create(SPL_T_DATE)
            ->Identifier("createdAt")
            ->InList("payments")
            ->name("Date")
            ->description("Payment Date")
            ->MicroData("http://schema.org/PaymentChargeSpecification", "validFrom")
            ->Group("Payments")
            ->isReadOnly();

        //====================================================================//
        // Payment Line Payment Identifier
        $this->fieldsFactory()->create(SPL_T_VARCHAR)
            ->Identifier("id")
            ->InList("payments")
            ->name("Number")
            ->description("Payment Number")
            ->MicroData("http://schema.org/Invoice", "paymentMethodId")
            ->Group("Payments")
            ->isReadOnly();

        //====================================================================//
        // Payment Line Payment Method
        $this->fieldsFactory()->create(SPL_T_VARCHAR)
            ->Identifier("method")
            ->InList("payments")
            ->name("Method")
            ->description("Payment Method")
            ->MicroData("http://schema.org/Invoice", "PaymentMethod")
            ->Group("Payments")
            ->AddChoices(array_flip(self::$knownMethods))
            ->isReadOnly();

        //====================================================================//
        // Payment Line Amount
        $this->fieldsFactory()->create(SPL_T_DOUBLE)
            ->Identifier("amount")
            ->InList("payments")
            ->name("Amount")
            ->description("Payment Amount")
            ->MicroData("http://schema.org/PaymentChargeSpecification", "price")
            ->Group("Payments")
            ->isReadOnly();

        //====================================================================//
        // Payment Line Currency
        $this->fieldsFactory()->create(SPL_T_DOUBLE)
            ->Identifier("currencyCode")
            ->InList("payments")
            ->name("Currency")
            ->description("Payment Currency")
            ->MicroData("http://schema.org/PaymentChargeSpecification", "priceCurrency")
            ->Group("Payments")
            ->isReadOnly();
    }

    /**
     * Read requested Field
     *
     * @param string $key       Input List Key
     * @param string $fieldName Field Identifier / Name
     */
    protected function getPaymentsFields($key, $fieldName)
    {
        //====================================================================//
        // Check if List field & Init List Array
        $fieldId = self::lists()->InitOutput($this->out, "payments", $fieldName);
        if (!$fieldId) {
            return;
        }
        //====================================================================//
        // Fill List with Data
        /** @var PaymentInterface $payment */
        foreach ($this->getOrderPayments() as $index => $payment) {
            //====================================================================//
            // READ Fields
            switch ($fieldId) {
                case 'createdAt':
                    $value = $payment->{'get'.ucfirst($fieldId)}()->format(SPL_T_DATECAST);

                    break;
                case 'id':
                case 'currencyCode':
                    $value = $payment->{'get'.ucfirst($fieldId)}();

                    break;
                case 'method':
                    $value = $this->getPaymentMethod($payment);

                    break;
                case 'amount':
                    $value = doubleval($payment->getAmount() / 100);

                    break;
                default:
                    return;
            }
            //====================================================================//
            // Insert Data in List
            self::lists()->Insert($this->out, "payments", $fieldName, $index, $value);
        }
        unset($this->in[$key]);
    }

    /**
     * Get Current Order Payments
     *
     * @return array
     */
    private function getOrderPayments(): array
    {
        //====================================================================//
        // Fetch Order Completed Payments
        $completedPayments = array();
        /** @var PaymentInterface $payment */
        foreach ($this->object->getPayments() as $payment) {
            if (!in_array($payment->getState(), static::$completedStates, true)) {
                continue;
            }
            $completedPayments[] = $payment;
        }
        //====================================================================//
        // Return Completed Order Payments
        return $completedPayments;
    }

    /**
     * Get Payment Item Method Name
     *
     * @param PaymentInterface $payment
     *
     * @return null|string
     */
    private function getPaymentMethod(PaymentInterface $payment): ?string
    {
        $method = $payment->getMethod();
        if (!$method) {
            return null;
        }

        if (array_key_exists((string) $method->getCode(), static::$knownMethods)) {
            return static::$knownMethods[$method->getCode()];
        }

        return "Unknown";
    }
}

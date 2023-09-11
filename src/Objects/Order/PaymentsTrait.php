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
    protected PaymentInterface $payment;

    /**
     * Known Payment Method Codes Names
     *
     * @var array
     */
    protected static array $knownMethods = array(
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
    protected static array $completedStates = array(
        PaymentInterface::STATE_AUTHORIZED,
        PaymentInterface::STATE_COMPLETED,
    );

    /**
     * Build Fields using FieldFactory
     */
    protected function buildPaymentsFields(): void
    {
        //====================================================================//
        // Payment Line Date
        $this->fieldsFactory()->create(SPL_T_DATE)
            ->identifier("createdAt")
            ->inList("payments")
            ->name("Date")
            ->description("Payment Date")
            ->microData("http://schema.org/PaymentChargeSpecification", "validFrom")
            ->group("Payments")
            ->isReadOnly()
        ;
        //====================================================================//
        // Payment Line Payment Identifier
        $this->fieldsFactory()->create(SPL_T_VARCHAR)
            ->identifier("id")
            ->inList("payments")
            ->name("Number")
            ->description("Payment Number")
            ->microData("http://schema.org/Invoice", "paymentMethodId")
            ->group("Payments")
            ->isReadOnly()
        ;
        //====================================================================//
        // Payment Line Payment Method
        $this->fieldsFactory()->create(SPL_T_VARCHAR)
            ->identifier("method")
            ->inList("payments")
            ->name("Method")
            ->description("Payment Method")
            ->microData("http://schema.org/Invoice", "PaymentMethod")
            ->group("Payments")
            ->addChoices(array_flip(self::$knownMethods))
            ->isReadOnly()
        ;
        //====================================================================//
        // Payment Line Amount
        $this->fieldsFactory()->create(SPL_T_DOUBLE)
            ->identifier("amount")
            ->inList("payments")
            ->name("Amount")
            ->description("Payment Amount")
            ->microData("http://schema.org/PaymentChargeSpecification", "price")
            ->group("Payments")
            ->isReadOnly()
        ;
        //====================================================================//
        // Payment Line Currency
        $this->fieldsFactory()->create(SPL_T_DOUBLE)
            ->identifier("currencyCode")
            ->inList("payments")
            ->name("Currency")
            ->description("Payment Currency")
            ->microData("http://schema.org/PaymentChargeSpecification", "priceCurrency")
            ->group("Payments")
            ->isReadOnly()
        ;
    }

    /**
     * Read requested Field
     *
     * @param string $key       Input List Key
     * @param string $fieldName Field Identifier / Name
     */
    protected function getPaymentsFields(string $key, string $fieldName): void
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
                    $value = (string) $payment->{'get'.ucfirst($fieldId)}();

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

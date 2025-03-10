<?php

declare(strict_types=1);

namespace Tino\Payment\Model\Ui;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Tino\Payment\Api\PaymentContentInterface;
use Tino\Payment\Gateway\Config\Config;
use Magento\Checkout\Model\Session as CheckoutSession;

class ConfigProvider
{
    const CODE = 'tino_payment';

    private Config $config;
    private CheckoutSession $checkoutSession;
    private PaymentContentInterface $paymentContent;

    public function __construct(
        Config $config,
        CheckoutSession $checkoutSession,
        PaymentContentInterface $paymentContent
    ) {
        $this->config = $config;
        $this->checkoutSession = $checkoutSession;
        $this->paymentContent = $paymentContent;
    }

    /**
     * @return \array[][]
     */
    public function getConfig(): array
    {
        return [
            'payment' => [
                self::CODE => [
                    'isActive' => $this->config->isActive(),
                    'sdkApiKey' => $this->config->getSdkApiKey(),
                    'externalIdAlias' => $this->config->getAliasExternalId(),
                    'taxvat' => $this->getTaxVatCustomer() ?: false,
                    'paymentContent' => $this->getPaymentContent()
                ],
            ]
        ];
    }

    /**
     * @return string
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function getTaxVatCustomer(): string
    {
        if ($this->config->getCnpjAttribute() == 'taxvat') {
            $customer = $this->checkoutSession->getQuote()->getCustomer();
            return $customer->getId() ? $customer->getTaxvat() : "";
        }

        return "";
    }

    /**
     * @return string
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function getPaymentContent(): string
    {
        $cnpj = $this->getCnpjByShippingAddress();
        $taxVatCustomer = $this->getTaxVatCustomer();

        if ($taxVatCustomer) {
            $cnpj = $taxVatCustomer;
        }

        $customer = $this->checkoutSession->getQuote()->getCustomer();
        $clientProfileData = [];

        if ($customer->getId()) {
            $shippingAddress = $this->checkoutSession->getQuote()->getShippingAddress();

            $clientProfileData = [
                "email" => $customer->getEmail(),
                "first_name" => $customer->getFirstname(),
                "last_name" => $customer->getLastname(),
                "document" => $customer->getTaxvat(),
                "document_type" => $this->isCpf($customer->getTaxvat()) ? 'cpf' : 'cnpj',
                "phone" => $shippingAddress ? $shippingAddress->getTelephone() : "",
                "trade_name" =>  $shippingAddress ? $shippingAddress->getCompany() : "",
            ];
        }

        $clientProfileData["cnpj"] = $cnpj;

        return $this->paymentContent->getPaymentContent($clientProfileData);
    }

    /**
     * @return string
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    private function getCnpjByShippingAddress(): string
    {
        $shippingAddress = $this->checkoutSession->getQuote()->getShippingAddress();
        if ($shippingAddress->getId()) {
            return $shippingAddress->getVatId() ?? "";
        }

        return "";
    }

    /**
     * @param $value
     * @return bool
     */
    private function isCpf($value): bool
    {
        $value = $value ? preg_replace('/[^0-9]/', '', (string) $value) : "";

        if ($value && strlen($value) == 11) {
            return true;
        }

        return false;
    }

}

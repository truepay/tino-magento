<?php

declare(strict_types=1);

namespace Tino\Payment\Model\Payment;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Serialize\Serializer\Json as JsonSerializer;
use Psr\Log\LoggerInterface;
use Tino\Payment\Api\ApiInterface;
use Tino\Payment\Api\PaymentContentInterface;
use Tino\Payment\Gateway\Config\Config;
use Magento\Store\Model\StoreManagerInterface;

class PaymentContent implements PaymentContentInterface
{
    private ApiInterface $api;
    private Config $config;
    private JsonSerializer $jsonSerializer;
    private LoggerInterface $logger;
    private StoreManagerInterface $storeManager;

    public function __construct(
        ApiInterface $api,
        Config $config,
        JsonSerializer $jsonSerializer,
        LoggerInterface $logger,
        StoreManagerInterface $storeManager
    ) {
        $this->api = $api;
        $this->config = $config;
        $this->jsonSerializer = $jsonSerializer;
        $this->logger = $logger;
        $this->storeManager = $storeManager;
    }

    /**
     * @param $clientProfileData
     * @return string
     * @throws NoSuchEntityException
     */
    public function getPaymentContent($clientProfileData): string
    {
        $response = [];

        $payload = [
            "seller" => [
                "name" => $this->storeManager->getStore()->getName(),
                "cnpj" => $this->config->getStoreCnpj()
            ],
            "client_profile_data" => $clientProfileData
        ];

        try {
            $response = $this->api->getPaymentContent($payload);
        } catch (\Exception $e){
            $this->logger->error($e->getMessage());
        }

        $responseFormatted = [
            'titlePayment' => $response['response']['title'] ?? "",
            'bannerPayment' => $response['response']['banner'] ?? "",
            'logoPayment' => $response['response']['background_image'] ?? ""
        ];

        return $this->jsonSerializer->serialize($responseFormatted);
    }
}

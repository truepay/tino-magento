<?php

declare(strict_types=1);

namespace Tino\Payment\Model\Webhook;

use Magento\Framework\Serialize\Serializer\Json as JsonSerializer;
use Magento\Payment\Model\Method\Logger;
use Psr\Log\LoggerInterface;
use Tino\Payment\Api\Data\WebhookInterface;
use Tino\Payment\Api\WebhookReceiverInterface;
use Tino\Payment\Model\ResourceModel\Webhook as WebhookResource;
use Tino\Payment\Model\WebhookFactory;
use Magento\Framework\Webapi\Rest\Request;

class WebhookReceiver implements WebhookReceiverInterface
{
    private WebhookFactory $webhookFactory;
    private WebhookResource $webhookResource;
    private LoggerInterface $logger;
    private Logger $loggerPayment;
    private JsonSerializer $jsonSerializer;
    private Request $request;

    public function __construct(
        LoggerInterface $logger,
        WebhookFactory $webhookFactory,
        WebhookResource $webhookResource,
        Logger $loggerPayment,
        JsonSerializer $jsonSerializer,
        Request $request
    ) {
        $this->logger = $logger;
        $this->webhookFactory = $webhookFactory;
        $this->webhookResource = $webhookResource;
        $this->loggerPayment = $loggerPayment;
        $this->jsonSerializer = $jsonSerializer;
        $this->request = $request;
    }

    /**
     * @inheritDoc
     */
    public function receiveOrderData(string $type): array
    {
        $payload = $this->request->getBodyParams();

        $this->loggerPayment->debug($payload);

        // Validate the webhook structure
        if (!$this->validatePayload($payload, $type)) {
            $this->loggerPayment->debug(['error' => 'Tino Payment - Invalid payload structure']);
            return [];
        }

        // Save the webhook to the database
        return $this->saveWebhook($payload, $type);
    }


    /**
     * @param array $payloadData
     * @param $type
     * @return bool
     */
    private function validatePayload(array $payloadData, $type): bool
    {
        if (!isset($payloadData['type'], $payloadData['data'][self::PAYMENT_RESERVATION_ID], $payloadData['data'][self::EXTERNAL_ID])) {
            return false;
        }

        if (!str_starts_with($payloadData['data'][self::EXTERNAL_ID], 'magento_')) {
            return false;
        }

        if (!in_array($type, self::TYPES_WEBHOOK)) {
            return false;
        }

        return true;
    }

    /**
     * @param $payloadData
     * @param $type
     * @return array
     */
    private function saveWebhook($payloadData, $type): array
    {
        $webhookData = $payloadData['data'];

        $webhook = $this->webhookFactory->create();
        $webhook->setType($type);
        $webhook->setPaymentReservationId($webhookData[self::PAYMENT_RESERVATION_ID]);
        $webhook->setExternalId($webhookData[self::EXTERNAL_ID]);
        $webhook->setStatus(WebhookInterface::STATUS_PENDING);

        try {
            $this->webhookResource->save($webhook);
        } catch (\Exception $e) {
            $this->logger->error('Error saving webhook: ' . $e->getMessage());
            return ['error' => 'Could not save webhook'];
        }

        return [];
    }
}

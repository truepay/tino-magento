<?php

declare(strict_types=1);

namespace Tino\Payment\Model\Webhook;

use Magento\Framework\DataObject;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\Data\TransactionInterface;
use Magento\Sales\Api\OrderStatusHistoryRepositoryInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Payment;
use Magento\Sales\Api\OrderRepositoryInterface;
use Psr\Log\LoggerInterface;
use Tino\Payment\Api\ApiInterface;
use Tino\Payment\Api\Data\WebhookInterface;
use Tino\Payment\Gateway\Config\Config;
use Tino\Payment\Model\ResourceModel\Webhook as WebhookResource;
use Tino\Payment\Model\ResourceModel\Webhook\Collection as WebhookCollection;
use Magento\Sales\Api\TransactionRepositoryInterface;
use Magento\Sales\Model\Order\Payment\Transaction\BuilderInterface;
use Magento\Sales\Model\ResourceModel\Order\Collection;
use Magento\Framework\Serialize\Serializer\Json as JsonSerializer;

class WebhookProcessor
{
    const PAYMENT_RESERVATION_CREATED = 'payment_reservation_created';
    const PAYMENT_RESERVATION_CANCELED = 'limit_reservation_canceled';

    private WebhookCollection $webhookCollection;
    private WebhookResource $webhookResource;
    private OrderRepositoryInterface $orderRepository;
    private LoggerInterface $logger;
    private ApiInterface $api;
    private OrderStatusHistoryRepositoryInterface $orderStatusHistoryRepository;
    private TransactionRepositoryInterface $transactionRepository;
    private BuilderInterface $transactionBuilder;
    private Collection $orderCollection;
    private Config $config;
    private JsonSerializer $jsonSerializer;

    public function __construct(
        WebhookCollection $webhookCollection,
        WebhookResource $webhookResource,
        OrderRepositoryInterface $orderRepository,
        LoggerInterface $logger,
        Collection $orderCollection,
        ApiInterface $api,
        TransactionRepositoryInterface $transactionRepository,
        BuilderInterface $transactionBuilder,
        OrderStatusHistoryRepositoryInterface $orderStatusHistoryRepository,
        Config $config,
        JsonSerializer $jsonSerializer
    ) {
        $this->webhookCollection = $webhookCollection;
        $this->webhookResource = $webhookResource;
        $this->orderRepository = $orderRepository;
        $this->logger = $logger;
        $this->orderCollection = $orderCollection;
        $this->api = $api;
        $this->transactionRepository = $transactionRepository;
        $this->transactionBuilder = $transactionBuilder;
        $this->orderStatusHistoryRepository = $orderStatusHistoryRepository;
        $this->config = $config;
        $this->jsonSerializer = $jsonSerializer;
    }

    /**
     * @return void
     * @throws AlreadyExistsException
     * @throws InputException
     * @throws NoSuchEntityException
     * @throws CouldNotSaveException
     */
    public function processOrdersWebhooks(): void
    {
        $webhookCollection = $this->webhookCollection->addFieldToFilter("status", WebhookInterface::STATUS_PENDING);

        /** @var WebhookInterface $webhook */
        foreach ($webhookCollection as $webhook) {

            $externalId = $webhook->getExternalId();

            /** @var Payment $payment */
            $payment = $this->getPaymentOrder($externalId);

            if (!$payment && $webhook->getType() != self::PAYMENT_RESERVATION_CANCELED) {
                // CANCEL RESERVATION IN TINO API, IF ORDER NOT FOUND IN MAGENTO.
                $this->api->cancelReservation($webhook->getPaymentReservationId());

                $this->completeWebhook($webhook);
                continue;
            }

            if ($webhook->getType() == self::PAYMENT_RESERVATION_CREATED) {
                $this->processReservationCreated($payment, $webhook);
            }

            if ($webhook->getType() == self::PAYMENT_RESERVATION_CANCELED) {
                $this->processReservationCanceled($payment, $webhook);
            }
        }
    }

    /**
     * @param Payment $payment
     * @param $webhook
     * @return void
     */
    private function processReservationCreated(Payment $payment, $webhook): void
    {
        /** @var OrderInterface $order */
        $order = $payment->getOrder();

        if ($order->getStatus() == Order::STATE_CANCELED) {
            $this->api->cancelReservation($webhook->getPaymentReservationId());
            $this->completeWebhook($webhook);
            return;
        }

        $this->generateTransaction($payment, $order);

        $order->setState(Order::STATE_PENDING_PAYMENT);
        $order->setStatus(Order::STATE_PENDING_PAYMENT);

        try {
            $this->orderRepository->save($order);
        } catch (\Exception $e) {
            $this->logger->critical($e->getMessage());
        }

        $this->completeWebhook($webhook);
        $this->sendOrderInApi($order, $webhook);
    }

    /**
     * @param $order
     * @param $webhook
     * @return void
     */
    private function sendOrderInApi($order, $webhook): void
    {
        $reservationId = $webhook->getPaymentReservationId();
        $data = [
            "additional_data" => [
                "external_order" => [
                    "order_id" => $order->getId(),
                    "increment_id" => $order->getIncrementId()
                ]
            ]
        ];

        $this->logger->info("Tino Payment - Order send body {$reservationId} " . $this->jsonSerializer->serialize($data));
        try {
            $response = $this->api->sendOrder($data, $reservationId);

            $this->logger->info("Tino Payment - Order Response body {$reservationId} " . $this->jsonSerializer->serialize($response));

            if ($response['status_code'] != 200) {
                $errorCode = '';

                if (isset($response['response']['code'])) {
                    $errorCode = $response['response']['code'];
                }

                $comment = __('Order not send to Tino Api. Error code: %1', $errorCode);
                $historyItem = $order->addCommentToStatusHistory($comment, false, true);
                $historyItem->setIsCustomerNotified(0);

                $this->orderStatusHistoryRepository->save($historyItem);
            }
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
        }
    }

    /**
     * @param Payment $payment
     * @param $order
     * @return void
     */
    private function generateTransaction(Payment $payment, $order): void
    {
        $transaction = $this->transactionBuilder
            ->setPayment($payment)
            ->setOrder($order)
            ->setTransactionId($payment->getAdditionalInformation("reservation_id"))
            ->setFailSafe(true)
            ->build(TransactionInterface::TYPE_AUTH);

        $transaction->setIsClosed(false);

        try {
            $this->transactionRepository->save($transaction);
        } catch (\Exception $e) {
            $this->logger->critical($e->getMessage());
        }
    }

    /**
     * @param $payment
     * @param $webhook
     * @return void
     * @throws AlreadyExistsException|CouldNotSaveException
     */
    private function processReservationCanceled($payment, $webhook): void
    {
        /** @var OrderInterface $order */
        $order = $payment->getOrder();

        try {
            $this->cancelOrder($order);
        } catch (\Exception $e) {
            $this->logger->critical($e->getMessage());
        }

        $this->completeWebhook($webhook);
    }

    /**
     * @param $order
     * @return void
     * @throws CouldNotSaveException
     */
    private function cancelOrder($order): void
    {
        $comment = __('Order Cancelled by Tino via Webhook.');
        $historyItem = $order->addCommentToStatusHistory($comment, false, true);
        $historyItem->setIsCustomerNotified(0);

        try {
            $order->cancel();
            $this->orderRepository->save($order);
            $this->orderStatusHistoryRepository->save($historyItem);
        } catch (AlreadyExistsException|NoSuchEntityException|InputException $e) {
            $this->logger->critical($e->getMessage());
            $comment = __('Order not cancelled. Please, verify logs archive.');
            $historyItem = $order->addCommentToStatusHistory($comment, false, true);
            $historyItem->setIsCustomerNotified(0);
            $this->orderStatusHistoryRepository->save($historyItem);
        }
    }

    /**
     * @param $webhook
     * @return void
     */
    private function completeWebhook($webhook): void
    {
        $webhook->setStatus(WebhookInterface::STATUS_COMPLETE);

        try {
            $this->webhookResource->save($webhook);
        } catch (\Exception $e) {
            $this->logger->critical($e->getMessage());
        }
    }

    /**
     * @param $externalId
     * @return DataObject
     */
    private function getPaymentOrder($externalId)
    {
        $alias = $this->config->getAliasExternalId();
        $quoteId = preg_replace("/^{$alias}/", "", $externalId);

        $order = $this->orderCollection
            ->addFieldToFilter('quote_id', $quoteId)
            ->setPageSize(1)
            ->getFirstItem();

        return $order->getPayment();
    }
}

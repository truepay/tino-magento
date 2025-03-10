<?php

declare(strict_types=1);

namespace Tino\Payment\Model\Invoice;

use Magento\Framework\DB\Transaction;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Serialize\Serializer\Json as JsonSerializer;
use Magento\Sales\Api\OrderPaymentRepositoryInterface;
use Magento\Sales\Api\OrderStatusHistoryRepositoryInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Email\Sender\InvoiceSender;
use Magento\Sales\Model\Order\Invoice;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Service\InvoiceService;
use Psr\Log\LoggerInterface;
use Tino\Payment\Api\ApiInterface;
use Tino\Payment\Api\Data\InvoiceNfeInterface;
use Tino\Payment\Model\InvoiceManagement;

class InvoiceProcessor
{
    private ApiInterface $api;
    private Order $order;
    private OrderPaymentRepositoryInterface $paymentRepository;
    private JsonSerializer $jsonSerializer;
    private LoggerInterface $logger;
    private InvoiceService $invoiceService;
    private Transaction $transaction;
    private InvoiceSender $invoiceSender;
    private ManagerInterface $eventManager;
    private OrderStatusHistoryRepositoryInterface $orderStatusHistoryRepository;
    private OrderRepositoryInterface $orderRepository;
    private InvoiceManagement $invoiceManagement;

    public function __construct(
        ApiInterface $api,
        Order $order,
        OrderPaymentRepositoryInterface $paymentRepository,
        JsonSerializer $jsonSerializer,
        LoggerInterface $logger,
        InvoiceService $invoiceService,
        Transaction $transaction,
        InvoiceSender $invoiceSender,
        ManagerInterface $eventManager,
        OrderStatusHistoryRepositoryInterface $orderStatusHistoryRepository,
        OrderRepositoryInterface $orderRepository,
        InvoiceManagement $invoiceManagement,
    ) {
        $this->api = $api;
        $this->order = $order;
        $this->paymentRepository = $paymentRepository;
        $this->jsonSerializer = $jsonSerializer;
        $this->logger = $logger;
        $this->invoiceService = $invoiceService;
        $this->transaction = $transaction;
        $this->invoiceSender = $invoiceSender;
        $this->eventManager = $eventManager;
        $this->orderStatusHistoryRepository = $orderStatusHistoryRepository;
        $this->orderRepository = $orderRepository;
        $this->invoiceManagement = $invoiceManagement;
    }

    /**
     * @param $incrementId
     * @param $nfes
     * @param $lastBatch
     * @return void
     */
    public function processNfe($incrementId, $nfes, $lastBatch): void
    {
        $order = $this->order->loadByIncrementId($incrementId);
        $reservationId = $order->getPayment()->getAdditionalInformation("reservation_id");
        $apiData = $this->buildData($nfes, $lastBatch);

        try {
            $response = $this->api->sendNfe($apiData, $reservationId);

            if ($response['status_code'] == 200 && isset($response['response']['invoices'])) {

                $responseApi = $response['response']['invoices'];
                $invoiceExternalId = $this->invoiceManagement->saveInvoiceWithItems($responseApi, $nfes, $order);
                $this->generateInvoice($order, $nfes, $lastBatch, $invoiceExternalId);
            }

        } catch (\Exception $exception) {
            $this->logger->error($exception->getMessage());
        }
    }

    /**
     * @param $nfes
     * @param $lastBatch
     * @return array
     */
    private function buildData($nfes, $lastBatch): array
    {
        $nfesProcess = [];

        /** @var InvoiceNfeInterface $nfe */
        foreach ($nfes as $nfe) {
            $nfesProcess[] = [
                "amount_cents" => $nfe->getAmountCents(),
                "external_id" => $nfe->getNfExternalId(),
                "notes" => $nfe->getNotes() ?? "",
                "data" => $nfe->getNfData()
            ];
        }

        return [
            "lastBatch" => $lastBatch,
            "nfes" => $nfesProcess
        ];
    }

    /**
     * @param Order $order
     * @param $nfes
     * @param bool $lastBatch
     * @param string $invoiceExternalId
     * @return void
     * @throws LocalizedException
     */
    private function generateInvoice(Order $order, $nfes, bool $lastBatch , string $invoiceExternalId = ""): void
    {
        if (!$order->canInvoice()) {
            return;
        }

        $itemsToInvoice = [];

        foreach ($nfes as $nfe) {
            foreach ($nfe->getItems() as $item) {
                $itemsToInvoice[$item->getItemId()] = $item->getQty();
            }
        }

        $invoice = $this->invoiceService->prepareInvoice($order, $itemsToInvoice);
        $invoice->setRequestedCaptureCase(Invoice::CAPTURE_OFFLINE);
        $invoice->register();
        $invoice->setTransactionId($invoiceExternalId ?? $order->getPayment()->getAdditionalInformation("reservation_id"));
        $invoice->save();

        $transactionSave = $this->transaction
            ->addObject($invoice)
            ->addObject($invoice->getOrder());

        $transactionSave->save();
        $this->invoiceSender->send($invoice);

        $comment = __('Invoice generated by Tino #%1.', $invoice->getIncrementId());
        $historyItem = $order->addCommentToStatusHistory($comment);
        $historyItem->setIsCustomerNotified(0);

        $payment = $order->getPayment();
        if ($lastBatch) {
            $payment->setIsTransactionClosed(true)->registerCaptureNotification($order->getGrandTotal(), true);
        }

        try {
            $this->orderRepository->save($order);
            $this->orderStatusHistoryRepository->save($historyItem);
        } catch (\Exception $exception) {
            $this->logger->error($exception->getMessage());
        }

        $this->eventManager->dispatch('tino_payment_after_invoice_generated', ['order' => $order]);
    }
}

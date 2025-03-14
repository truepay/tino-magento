<?php

namespace Tino\Payment\Model;

use Tino\Payment\Api\InvoiceRepositoryInterface;
use Tino\Payment\Api\InvoiceItemRepositoryInterface;
use Tino\Payment\Api\Data\InvoiceInterface;
use Tino\Payment\Api\Data\InvoiceItemInterface;
use Psr\Log\LoggerInterface;
use Tino\Payment\Model\InvoiceFactory as InvoiceFactory;
use Tino\Payment\Model\InvoiceItemFactory as InvoiceItemFactory;
use Magento\Framework\Exception\NoSuchEntityException;

class InvoiceManagement
{
    private InvoiceRepositoryInterface $invoiceRepository;
    private InvoiceItemRepositoryInterface $invoiceItemRepository;
    private LoggerInterface $logger;
    private InvoiceItemFactory $invoiceItemFactory;
    private InvoiceFactory $invoiceFactory;

    public function __construct(
        InvoiceRepositoryInterface $invoiceRepository,
        InvoiceItemRepositoryInterface $invoiceItemRepository,
        InvoiceFactory $invoiceFactory,
        InvoiceItemFactory $invoiceItemFactory,
        LoggerInterface $logger
    ) {
        $this->invoiceRepository = $invoiceRepository;
        $this->invoiceItemRepository = $invoiceItemRepository;
        $this->invoiceItemFactory = $invoiceItemFactory;
        $this->invoiceFactory = $invoiceFactory;
        $this->logger = $logger;
    }

    /**
     * @param array $invoices
     * @param $nfes
     * @param $order
     * @return string
     */
    public function saveInvoiceWithItems(array $invoices, $nfes, $order)
    {
        $invoiceExternalId = "";

        try {
            foreach ($invoices as $invoiceApi) {
                /** @var InvoiceInterface $invoice */
                $invoice = $this->invoiceFactory->create();
                $invoice->setExternalIdInvoice($invoiceApi['externalId']);
                $invoice->setIncrementId($order->getIncrementId());
                $invoice->setAmount($invoiceApi['amountCents']);


            $savedInvoice = $this->invoiceRepository->save($invoice);

            foreach ($nfes as $nfesApi) {
                foreach ($nfesApi->getItems() as $itemData) {
                    /** @var InvoiceItemInterface $invoiceItem */
                    $invoiceItem = $this->invoiceItemFactory->create();
                    $invoiceItem->setItemId($itemData->getItemId());
                    $invoiceItem->setParentId($savedInvoice->getId());
                    $invoiceItem->setQty($itemData->getQty());

                    $this->invoiceItemRepository->save($invoiceItem);
                }
            }

            $invoiceExternalId = $savedInvoice->getExternalIdInvoice();
        }
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
        }

        return $invoiceExternalId;
    }

    /**
     * @param string $incrementId
     * @param $items
     * @return array
     * @throws NoSuchEntityException
     */
    public function getInvoicesByIncrementIdAndItemId(string $incrementId, $items): array
    {
        try {
            $invoiceCollection = $this->invoiceRepository->getListByIncrementId($incrementId);

            if ($invoiceCollection->getSize() === 0) {
                throw new NoSuchEntityException(__('No invoice found for Increment ID %1.', $incrementId));
            }

            $invoiceData = [];

            /** @var Invoice $invoice */
            foreach ($invoiceCollection as $invoice) {
                foreach ($items as $item) {

                    $invoiceId = $invoice->getId();
                    $invoiceItemsCollection = $this->invoiceItemRepository->getListByParentIdAndItemId($invoiceId, $item->getOrderItemId());

                    if ($invoiceItemsCollection->getSize() > 0) {
                        $invoiceData = [
                            'amount' => $invoice->getAmount(),
                            'invoice_external_id' => $invoice->getExternalIdInvoice(),
                            'invoice_id' => $invoiceId
                        ];
                        break;
                    }
                }
            }

            if (empty($invoiceData)) {
                throw new NoSuchEntityException(__('No invoice found for Increment ID %1.', $incrementId));
            }

            return $invoiceData;
        } catch (\Exception $e) {
            $this->logger->error(__($e->getMessage()));
            throw new NoSuchEntityException(__('Error fetching invoices.'));
        }
    }
}

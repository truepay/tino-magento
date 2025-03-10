<?php

declare(strict_types=1);

namespace Tino\Payment\Gateway\Request;

use Magento\Payment\Gateway\Request\BuilderInterface;
use Magento\Payment\Gateway\Helper;
use Magento\Framework\Exception\LocalizedException;
use Tino\Payment\Api\Data\InvoiceInterface;
use Tino\Payment\Api\InvoiceRepositoryInterface;
use Tino\Payment\Model\InvoiceManagement;

class RefundDataBuilder implements BuilderInterface
{

    private InvoiceManagement $invoiceManagement;
    private InvoiceRepositoryInterface $invoiceRepository;

    public function __construct(
        InvoiceManagement $invoiceManagement,
        InvoiceRepositoryInterface $invoiceRepository
    ) {
        $this->invoiceManagement = $invoiceManagement;
        $this->invoiceRepository = $invoiceRepository;
    }

    /**
     * @throws LocalizedException
     */
    public function build(array $buildSubject): array
    {
        $payment = Helper\SubjectReader::readPayment($buildSubject)->getPayment();
        $order = $payment->getOrder();

        $items = $payment->getCreditMemo()->getItems();
        $invoice = $this->invoiceManagement->getInvoicesByIncrementIdAndItemId($order->getIncrementId(), $items);

        $grandTotalCreditMemo = $payment->getCreditMemo()->getGrandTotal() * 100;
        $fullInvoice = $order->getTotalRefunded() >= $order->getGrandTotal();
        $newAmountInvoice = $invoice['amount'] - $grandTotalCreditMemo;

        if (!$fullInvoice) {
            /** @var InvoiceInterface $invoiceNf */
            $invoiceNf = $this->invoiceRepository->getById($invoice['invoice_id']);
            $invoiceNf->setAmount($newAmountInvoice);
            $this->invoiceRepository->save($invoiceNf);
        }

        return [
            "is_full_invoice" => $fullInvoice,
            "new_amount_invoice" => $newAmountInvoice,
            "invoice_external_id" => $invoice['invoice_external_id']
        ];
    }
}

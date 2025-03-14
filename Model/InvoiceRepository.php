<?php
namespace Tino\Payment\Model;

use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Tino\Payment\Api\InvoiceRepositoryInterface;
use Tino\Payment\Api\Data\InvoiceInterface;
use Tino\Payment\Model\ResourceModel\Invoice as InvoiceResource;
use Tino\Payment\Model\InvoiceFactory;
use Magento\Framework\Api\SearchResultsInterfaceFactory;
use Psr\Log\LoggerInterface;

class InvoiceRepository implements InvoiceRepositoryInterface
{
    private InvoiceResource $invoiceResource;
    private InvoiceFactory $invoiceFactory;
    private SearchResultsInterfaceFactory $searchResultsFactory;
    private LoggerInterface $logger;

    public function __construct(
        InvoiceResource $invoiceResource,
        InvoiceFactory $invoiceFactory,
        SearchResultsInterfaceFactory $searchResultsFactory,
        LoggerInterface $logger
    ) {
        $this->invoiceResource = $invoiceResource;
        $this->invoiceFactory = $invoiceFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->logger = $logger;
    }

    /**
     * @inheritDoc
     */
    public function getById($id)
    {
        $invoice = $this->invoiceFactory->create();
        $this->invoiceResource->load($invoice, $id);

        if (!$invoice->getId()) {
            throw new NoSuchEntityException(__('Invoice with ID %1 not found.', $id));
        }

        return $invoice;
    }

    /**
     * @inheritDoc
     */
    public function getListByIncrementId(string $incrementId)
    {
        $invoiceCollection = $this->invoiceFactory->create()->getCollection()
            ->addFieldToFilter('increment_id', $incrementId);

        return $invoiceCollection;
    }

    /**
     * @inheritDoc
     */
    public function save(InvoiceInterface $invoice)
    {
        try {
            $this->invoiceResource->save($invoice);
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            throw new CouldNotSaveException(__('Unable to save invoice.'));
        }

        return $invoice;
    }

    /**
     * @inheritDoc
     */
    public function delete(InvoiceInterface $invoice)
    {
        try {
            $this->invoiceResource->delete($invoice);
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            throw new CouldNotSaveException(__('Unable to delete invoice.'));
        }

        return true;
    }
}

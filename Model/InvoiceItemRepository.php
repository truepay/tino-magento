<?php
namespace Tino\Payment\Model;

use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Tino\Payment\Api\InvoiceItemRepositoryInterface;
use Tino\Payment\Api\Data\InvoiceItemInterface;
use Tino\Payment\Model\ResourceModel\InvoiceItem as InvoiceItemResource;
use Tino\Payment\Model\InvoiceItemFactory;
use Magento\Framework\Api\SearchResultsInterfaceFactory;
use Psr\Log\LoggerInterface;

class InvoiceItemRepository implements InvoiceItemRepositoryInterface
{
    private InvoiceItemResource $invoiceItemResource;
    private InvoiceItemFactory $invoiceItemFactory;
    private SearchResultsInterfaceFactory $searchResultsFactory;
    private LoggerInterface $logger;

    public function __construct(
        InvoiceItemResource $invoiceItemResource,
        InvoiceItemFactory $invoiceItemFactory,
        SearchResultsInterfaceFactory $searchResultsFactory,
        LoggerInterface $logger
    ) {
        $this->invoiceItemResource = $invoiceItemResource;
        $this->invoiceItemFactory = $invoiceItemFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->logger = $logger;
    }

    /**
     * @inheritDoc
     */
    public function getByParentId($itemId): InvoiceItemInterface
    {
        $invoiceItem = $this->invoiceItemFactory->create();
        $this->invoiceItemResource->load($invoiceItem, $itemId);

        if (!$invoiceItem->getItemId()) {
            throw new NoSuchEntityException(__('Invoice item with ID %1 not found.', $itemId));
        }

        return $invoiceItem;
    }

    /**
     * @inheritDoc
     */
    public function getListByParentIdAndItemId(int $parentId, int $itemId)
    {
        $invoiceItemCollection = $this->invoiceItemFactory->create()->getCollection()
            ->addFieldToFilter('parent_id', $parentId)
            ->addFieldToFilter('item_id', $itemId);

        return $invoiceItemCollection;
    }

    /**
     * @inheritDoc
     */
    public function save(InvoiceItemInterface $invoiceItem)
    {
        try {
            $this->invoiceItemResource->save($invoiceItem);
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            throw new CouldNotSaveException(__('Unable to save invoice item.'));
        }

        return $invoiceItem;
    }

    /**
     * @inheritDoc
     */
    public function delete(InvoiceItemInterface $invoiceItem)
    {
        try {
            $this->invoiceItemResource->delete($invoiceItem);
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            throw new CouldNotSaveException(__('Unable to delete invoice item.'));
        }

        return true;
    }
}

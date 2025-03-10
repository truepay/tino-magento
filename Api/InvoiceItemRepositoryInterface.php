<?php
namespace Tino\Payment\Api;

use Magento\Framework\Exception\NoSuchEntityException;
use Tino\Payment\Api\Data\InvoiceItemInterface;
use Tino\Payment\Model\ResourceModel\InvoiceItem\Collection;

interface InvoiceItemRepositoryInterface
{
    /**
     * @param $itemId
     * @return InvoiceItemInterface
     * @throws NoSuchEntityException
     */
    public function getByParentId($itemId): InvoiceItemInterface;

    /**
     * @param int $parentId
     * @param int $itemId
     * @return Collection
     */
    public function getListByParentIdAndItemId(int $parentId, int $itemId);

    /**
     * @param InvoiceItemInterface $invoiceItem
     * @return InvoiceItemInterface
     */
    public function save(InvoiceItemInterface $invoiceItem);


    /**
     * @param InvoiceItemInterface $invoiceItem
     * @return bool
     */
    public function delete(InvoiceItemInterface $invoiceItem);
}

<?php
namespace Tino\Payment\Api;

use Magento\Framework\Exception\NoSuchEntityException;
use Tino\Payment\Api\Data\InvoiceInterface;
use Tino\Payment\Model\ResourceModel\Invoice\Collection;

interface InvoiceRepositoryInterface
{

    /**
     * @param $id
     * @return InvoiceInterface
     */
    public function getById($id);

    /**
     * @param string $incrementId
     * @return Collection
     * @throws NoSuchEntityException
     */
    public function getListByIncrementId(string $incrementId);

    /**
     * @param InvoiceInterface $invoice
     * @return InvoiceInterface
    */
    public function save(InvoiceInterface $invoice);

    /**
     * @param InvoiceInterface $invoice
     * @return bool
     */
    public function delete(InvoiceInterface $invoice);
}

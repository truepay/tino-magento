<?php
namespace Tino\Payment\Model;

use Magento\Framework\Model\AbstractModel;
use Tino\Payment\Api\Data\InvoiceInterface;
use Tino\Payment\Model\ResourceModel\Invoice as InvoiceResource;

class Invoice extends AbstractModel implements InvoiceInterface
{
    protected function _construct()
    {
        $this->_init(InvoiceResource::class);
    }

    /**
     * @inheritDoc
     */
    public function getId()
    {
        return $this->getData(self::ID);
    }

    /**
     * @inheritDoc
     */
    public function getExternalIdInvoice()
    {
        return $this->getData(self::EXTERNAL_ID_INVOICE);
    }

    /**
     * @inheritDoc
     */
    public function getIncrementId()
    {
        return $this->getData(self::INCREMENT_ID);
    }

    /**
     * @inheritDoc
     */
    public function getAmount()
    {
        return $this->getData(self::AMOUNT);
    }

    /**
     * @inheritDoc
     */
    public function getCreatedAt()
    {
        return $this->getData(self::CREATED_AT);
    }

    /**
     * @inheritDoc
     */
    public function getUpdatedAt()
    {
        return $this->getData(self::UPDATED_AT);
    }

    /**
     * @inheritDoc
     */
    public function setId($id)
    {
        return $this->setData(self::ID, $id);
    }

    /**
     * @inheritDoc
     */
    public function setExternalIdInvoice($externalIdInvoice)
    {
        return $this->setData(self::EXTERNAL_ID_INVOICE, $externalIdInvoice);
    }

    /**
     * @inheritDoc
     */
    public function setIncrementId($incrementId)
    {
        return $this->setData(self::INCREMENT_ID, $incrementId);
    }

    /**
     * @inheritDoc
     */
    public function setAmount($amount)
    {
        return $this->setData(self::AMOUNT, $amount);
    }

    /**
     * @inheritDoc
     */
    public function setCreatedAt($createdAt)
    {
        return $this->setData(self::CREATED_AT, $createdAt);
    }

    /**
     * @inheritDoc
     */
    public function setUpdatedAt($updatedAt)
    {
        return $this->setData(self::UPDATED_AT, $updatedAt);
    }
}

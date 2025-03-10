<?php

namespace Tino\Payment\Model\Data;

use Tino\Payment\Api\Data\InvoiceNfeInterface;
use Magento\Framework\DataObject;

class InvoiceNfe extends DataObject implements InvoiceNfeInterface
{
    /**
     * @inheritdoc
     */
    public function getAmountCents()
    {
        return $this->getData(self::AMOUNT_CENTS);
    }

    /**
     * @inheritdoc
     */
    public function setAmountCents($amountCents)
    {
        return $this->setData(self::AMOUNT_CENTS, $amountCents);
    }

    /**
     * @inheritdoc
     */
    public function getNfData()
    {
        return $this->getData(self::NF_DATA);
    }

    /**
     * @inheritdoc
     */
    public function setNfData($nfData)
    {
        return $this->setData(self::NF_DATA, $nfData);
    }

    /**
     * @inheritdoc
     */
    public function getNfExternalId()
    {
        return $this->getData(self::NF_EXTERNAL_ID);
    }

    /**
     * @inheritdoc
     */
    public function setNfExternalId($nfExternalId)
    {
        return $this->setData(self::NF_EXTERNAL_ID, $nfExternalId);
    }

    /**
     * @inheritdoc
     */
    public function getNotes()
    {
        return $this->getData(self::NOTES);
    }

    /**
     * @inheritdoc
     */
    public function setNotes($notes = "")
    {
        return $this->setData(self::NOTES, $notes);
    }

    /**
     * @inheritdoc
     */
    public function getItems()
    {
        return $this->getData(self::ITEMS) ?? [];
    }

    /**
     * @inheritdoc
     */
    public function setItems(array $items)
    {
        return $this->setData(self::ITEMS, $items);
    }
}

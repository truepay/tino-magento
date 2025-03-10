<?php

namespace Tino\Payment\Model\Data;

use Tino\Payment\Api\Data\InvoiceItemApiInterface;
use Magento\Framework\DataObject;

class InvoiceItem extends DataObject implements InvoiceItemApiInterface
{
    /**
     * @inheritdoc
     */
    public function getItemId()
    {
        return $this->getData(self::ITEM_ID);
    }

    /**
     * @inheritdoc
     */
    public function setItemId($itemId)
    {
        return $this->setData(self::ITEM_ID, $itemId);
    }

    /**
     * @inheritdoc
     */
    public function getQty()
    {
        return $this->getData(self::QTY);
    }

    /**
     * @inheritdoc
     */
    public function setQty($qty)
    {
        return $this->setData(self::QTY, $qty);
    }
}

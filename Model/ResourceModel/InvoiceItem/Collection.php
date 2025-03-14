<?php

declare(strict_types=1);

namespace Tino\Payment\Model\ResourceModel\InvoiceItem;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Tino\Payment\Model\InvoiceItem;
use Tino\Payment\Model\ResourceModel\InvoiceItem as InvoiceItemResource;

class Collection extends AbstractCollection
{
    protected function _construct()
    {
        $this->_init(InvoiceItem::class, InvoiceItemResource::class);
    }
}

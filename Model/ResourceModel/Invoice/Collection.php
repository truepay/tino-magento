<?php

declare(strict_types=1);

namespace Tino\Payment\Model\ResourceModel\Invoice;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Tino\Payment\Model\Invoice;
use Tino\Payment\Model\ResourceModel\Invoice as InvoiceResource;

class Collection extends AbstractCollection
{
    protected function _construct()
    {
        $this->_init(Invoice::class, InvoiceResource::class);
    }
}

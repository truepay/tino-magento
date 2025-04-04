<?php
namespace Tino\Payment\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Invoice extends AbstractDb
{
    protected function _construct()
    {
        $this->_init('tino_invoice', 'id');
    }
}

<?php

declare(strict_types=1);

namespace Tino\Payment\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Webhook extends AbstractDb
{
    protected function _construct()
    {
        $this->_init('tino_webhooks', 'id');
    }
}

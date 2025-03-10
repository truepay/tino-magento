<?php

declare(strict_types=1);

namespace Tino\Payment\Model\ResourceModel\Webhook;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Tino\Payment\Model\Webhook;
use Tino\Payment\Model\ResourceModel\Webhook as WebhookResource;

class Collection extends AbstractCollection
{
    protected function _construct()
    {
        $this->_init(Webhook::class, WebhookResource::class);
    }
}

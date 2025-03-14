<?php

namespace Tino\Payment\Model\Data;

use Magento\Framework\DataObject;
use Tino\Payment\Api\Data\WebhookApiInterface;
use Tino\Payment\Api\Data\WebhookApiDataInterface;

class WebhookApi extends DataObject implements WebhookApiInterface
{
    /**
     * @inheritdoc
     */
    public function getType()
    {
        return $this->getData(self::TYPE);
    }

    /**
     * @inheritdoc
     */
    public function setType($type)
    {
        return $this->setData(self::TYPE, $type);
    }
}

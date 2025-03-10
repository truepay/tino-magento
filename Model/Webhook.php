<?php

declare(strict_types=1);

namespace Tino\Payment\Model;

use Magento\Framework\Model\AbstractModel;
use Tino\Payment\Api\Data\WebhookInterface;
use Tino\Payment\Model\ResourceModel\Webhook as WebhookResource;

class Webhook extends AbstractModel implements WebhookInterface
{
    protected function _construct()
    {
        $this->_init(WebhookResource::class);
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
    public function setId($id)
    {
        return $this->setData(self::ID, $id);
    }

    /**
     * @inheritDoc
     */
    public function getType()
    {
        return $this->getData(self::TYPE);
    }

    /**
     * @inheritDoc
     */
    public function setType($type)
    {
        return $this->setData(self::TYPE, $type);
    }

    /**
     * @inheritDoc
     */
    public function getPaymentReservationId()
    {
        return $this->getData(self::PAYMENT_RESERVATION_ID);
    }

    /**
     * @inheritDoc
     */
    public function setPaymentReservationId($paymentReservationId)
    {
        return $this->setData(self::PAYMENT_RESERVATION_ID, $paymentReservationId);
    }

    /**
     * @inheritDoc
     */
    public function getExternalId()
    {
        return $this->getData(self::EXTERNAL_ID);
    }

    /**
     * @inheritDoc
     */
    public function setExternalId($externalId)
    {
        return $this->setData(self::EXTERNAL_ID, $externalId);
    }

    /**
     * @inheritDoc
     */
    public function getStatus()
    {
        return $this->getData(self::STATUS);
    }

    /**
     * @inheritDoc
     */
    public function setStatus($status)
    {
        return $this->setData(self::STATUS, $status);
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
    public function setCreatedAt($createdAt)
    {
        return $this->setData(self::CREATED_AT, $createdAt);
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
    public function setUpdatedAt($updatedAt)
    {
        return $this->setData(self::UPDATED_AT, $updatedAt);
    }

}

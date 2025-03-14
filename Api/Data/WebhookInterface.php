<?php

namespace Tino\Payment\Api\Data;

interface WebhookInterface
{
    const ID = 'id';
    const TYPE = 'type';
    const PAYMENT_RESERVATION_ID = 'payment_reservation_id';
    const EXTERNAL_ID = 'external_id';
    const STATUS = 'status';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    const STATUS_PENDING = 'pending';
    const STATUS_COMPLETE = 'complete';

    /**
     * @return int
     */
    public function getId();

    /**
     * @param $id
     * @return int
     */
    public function setId($id);

    /**
     * @return string
     */
    public function getType();

    /**
     * @param $type
     * @return string
     */
    public function setType($type);


    /**
     * @return string
     */
    public function getPaymentReservationId();

    /**
     * @param $paymentReservationId
     * @return string
     */
    public function setPaymentReservationId($paymentReservationId);

    /**
     * @return string
     */
    public function getExternalId();

    /**
     * @param $externalId
     * @return string
     */
    public function setExternalId($externalId);

    /**
     * @return string
     */
    public function getStatus();

    /**
     * @param $status
     * @return string
     */
    public function setStatus($status);

    /**
     * @return string
     */
    public function getCreatedAt();

    /**
     * @param $createdAt
     * @return string
     */
    public function setCreatedAt($createdAt);

    /**
     * @return string
     */
    public function getUpdatedAt();

    /**
     * @param $updatedAt
     * @return string
     */
    public function setUpdatedAt($updatedAt);
}

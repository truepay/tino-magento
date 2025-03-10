<?php

namespace Tino\Payment\Api;

interface WebhookReceiverInterface
{
    const TYPES_WEBHOOK = [
        'payment_reservation_created',
        'limit_reservation_canceled'
    ];

    const EXTERNAL_ID = 'externalId';
    const PAYMENT_RESERVATION_ID = 'paymentReservationId';

    /**
     * Receive data webhook
     *
     * @param string $type
     *
     * @return array
     */
    public function receiveOrderData(string $type): array;
}

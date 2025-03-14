<?php

declare(strict_types=1);

namespace Tino\Payment\Api;

interface ApiInterface
{
    const ENDPOINT_LIMIT_RESERVATION = "v2/limit-reservations/";
    const ENDPOINT_INVOICES = "v1/invoices/";
    const ENDPOINT_STATEMENTS = "v1/statements/";
    const ENDPOINT_BANNER = "v2/banner";

    /**
     * Gets the limit reservation for an order by api tino
     *
     * @param string $orderId
     * @return array
     */
    public function getLimitReservation(string $orderId): array;

    /**
     * Cancel reservation in api tino
     *
     * @param string $reservationId
     * @return array
     */
    public function cancelReservation(string $reservationId): array;

    /**
     * Send nfe to api tino
     * @param $body
     * @param $reservationId
     * @return array
     */
    public function sendNfe($body, $reservationId): array;

    /**
     * Refund partial nfe in api tino
     * @param $body
     * @param $externalInvoiceId
     * @return array
     */
    public function refundNfePartial($body, $externalInvoiceId): array;

    /**
     * Refund fully nfe in api tino
     * @param $externalInvoiceId
     * @return array
     */
    public function refundNfeFully($externalInvoiceId): array;

    /**
     * Download Statements Report in api tino
     * @param $startDate
     * @param $endDate
     * @return array
     */
    public function downloadStatementsReport($type, $startDate, $endDate): array;

    /**
     * Get payment content banner tino in api
     * @param $body
     * @return array
     */
    public function getPaymentContent($body): array;
}

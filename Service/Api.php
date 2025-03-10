<?php

declare(strict_types=1);

namespace Tino\Payment\Service;

use Tino\Payment\Api\ApiInterface;

class Api extends ApiAbstract implements ApiInterface
{

    /**
     * {@inheritdoc}
     */
    public function getLimitReservation(string $orderId): array
    {
        $endpoint = self::ENDPOINT_LIMIT_RESERVATION . $orderId;
        return $this->request($endpoint);
    }

    /**
     * {@inheritdoc}
     */
    public function cancelReservation(string $reservationId): array
    {
        $endpoint = self::ENDPOINT_LIMIT_RESERVATION . $reservationId;
        return $this->request($endpoint, "DELETE");
    }

    /**
     * {@inheritdoc}
     */
    public function sendNfe($body, $reservationId): array
    {
        $endpoint = self::ENDPOINT_LIMIT_RESERVATION . $reservationId . "/" . "partial-bill";
        return $this->request($endpoint, "POST", [], $body);
    }

    /**
     * {@inheritdoc}
     */
    public function refundNfePartial($body, $externalInvoiceId): array
    {
        $endpoint = self::ENDPOINT_INVOICES . $externalInvoiceId;
        return $this->request($endpoint, "PATCH", [], $body);
    }

    /**
     * {@inheritdoc}
     */
    public function refundNfeFully($externalInvoiceId): array
    {
        $endpoint = self::ENDPOINT_INVOICES . $externalInvoiceId;
        return $this->request($endpoint, "DELETE");
    }

    /**
     * {@inheritdoc}
     */
    public function downloadStatementsReport($type, $startDate, $endDate): array
    {
        $endpoint = self::ENDPOINT_STATEMENTS . $type . "?startDate=" . $startDate . "&endDate=" . $endDate;
        return $this->request($endpoint);
    }

    /**
     * {@inheritdoc}
     */
    public function getPaymentContent($body): array
    {
        $endpoint = self::ENDPOINT_BANNER;
        return $this->requestBanner($endpoint, $body);
    }
}

<?php

namespace Tino\Payment\Api;

use Tino\Payment\Api\Data\InvoiceNfeInterface;

interface InvoiceReceiverInterface
{
    /**
     * Receive invoice data via API.
     *
     * @param string $incrementId
     * @param InvoiceNfeInterface[] $nfes
     * @param bool $lastBatch
     * @return array
     */
    public function receiveInvoice(
        string $incrementId,
        array $nfes,
        bool $lastBatch
    ): array;
}

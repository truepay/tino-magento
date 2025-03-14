<?php

namespace Tino\Payment\Api\Data;

use Tino\Payment\Api\Data\InvoiceNfeInterface;

interface InvoiceApiInterface
{
    const INCREMENT_ID = 'incrementId';
    const NFES = 'nfes';
    const LAST_BATCH = 'lastBatch';

    /**
     * Get the transaction ID.
     *
     * @return string
     */
    public function getIncrementId();

    /**
     * Set the transaction ID.
     *
     * @param string $incrementId
     * @return void
     */
    public function setIncrementId($incrementId);

    /**
     * Get the list of invoices (NFEs).
     *
     * @return \Tino\Payment\Api\Data\InvoiceNfeInterface[]
     */
    public function getNfes();

    /**
     * Set the list of invoices (NFEs).
     *
     * @param \Tino\Payment\Api\Data\InvoiceNfeInterface[] $nfes
     * @return void
     */
    public function setNfes(array $nfes);

    /**
     * Get whether this is the last batch.
     *
     * @return bool
     */
    public function getLastBatch();

    /**
     * Set whether this is the last batch.
     *
     * @param bool $lastBatch
     * @return void
     */
    public function setLastBatch($lastBatch);
}

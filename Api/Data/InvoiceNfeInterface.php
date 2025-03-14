<?php

namespace Tino\Payment\Api\Data;

use Tino\Payment\Api\Data\InvoiceItemApiInterface;

interface InvoiceNfeInterface
{
    const AMOUNT_CENTS = 'amount_cents';
    const NF_DATA = 'nf_data';
    const NF_EXTERNAL_ID = 'nf_external_id';
    const NOTES = 'notes';
    const ITEMS = 'items';

    /**
     * Get the total amount in cents.
     *
     * @return int
     */
    public function getAmountCents();

    /**
     * Set the total amount in cents.
     *
     * @param int $amountCents
     * @return void
     */
    public function setAmountCents($amountCents);

    /**
     * Get the invoice data.
     *
     * @return string
     */
    public function getNfData();

    /**
     * Set the invoice data.
     *
     * @param string $nfData
     * @return void
     */
    public function setNfData($nfData);

    /**
     * Get Nf external ID.
     *
     * @return string
     */
    public function getNfExternalId();

    /**
     * Set Nf external ID.
     *
     * @param string $nfExternalId
     * @return void
     */
    public function setNfExternalId($nfExternalId);

    /**
     * Get additional notes.
     *
     * @return string
     */
    public function getNotes();

    /**
     * Set additional notes.
     *
     * @param string $notes
     * @return void
     */
    public function setNotes($notes);

    /**
     * Get the list of invoice items.
     *
     * @return \Tino\Payment\Api\Data\InvoiceItemApiInterface[]
     */
    public function getItems();

    /**
     * Set the list of invoice items.
     *
     * @param \Tino\Payment\Api\Data\InvoiceItemApiInterface[] $items
     * @return void
     */
    public function setItems(array $items);
}

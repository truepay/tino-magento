<?php

namespace Tino\Payment\Api\Data;

interface InvoiceInterface
{
    const ID = 'id';
    const EXTERNAL_ID_INVOICE = 'external_id_invoice';
    const INCREMENT_ID = 'increment_id';
    const AMOUNT = 'amount';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    /**#@-*/

    /**
     * Get invoice ID.
     *
     * @return int|null
     */
    public function getId();

    /**
     * Get external invoice ID.
     *
     * @return string
     */
    public function getExternalIdInvoice();

    /**
     * Get increment ID.
     *
     * @return string
     */
    public function getIncrementId();

    /**
     * Get invoice amount.
     *
     * @return string
     */
    public function getAmount();

    /**
     * Get invoice creation date.
     *
     * @return string
     */
    public function getCreatedAt();

    /**
     * Get invoice last update date.
     *
     * @return string
     */
    public function getUpdatedAt();

    /**
     * Set invoice ID.
     *
     * @param int $id
     * @return $this
     */
    public function setId($id);

    /**
     * Set external invoice ID.
     *
     * @param string $externalIdInvoice
     * @return $this
     */
    public function setExternalIdInvoice($externalIdInvoice);

    /**
     * Set increment ID.
     *
     * @param string $incrementId
     * @return $this
     */
    public function setIncrementId($incrementId);

    /**
     * Set invoice amount.
     *
     * @param $amount
     * @return $this
     */
    public function setAmount($amount);

    /**
     * Set invoice creation date.
     *
     * @param string $createdAt
     * @return $this
     */
    public function setCreatedAt($createdAt);

    /**
     * Set invoice last update date.
     *
     * @param string $updatedAt
     * @return $this
     */
    public function setUpdatedAt($updatedAt);
}

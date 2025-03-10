<?php

namespace Tino\Payment\Api\Data;

interface InvoiceItemApiInterface
{
    const ITEM_ID = 'item_id';
    const QTY = 'qty';

    /**
     * Get the item ID.
     *
     * @return int
     */
    public function getItemId();

    /**
     * Set the item ID.
     *
     * @param int $itemId
     * @return void
     */
    public function setItemId($itemId);

    /**
     * Get the item quantity.
     *
     * @return int
     */
    public function getQty();

    /**
     * Set the item quantity.
     *
     * @param int $qty
     * @return void
     */
    public function setQty($qty);
}

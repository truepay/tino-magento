<?php
namespace Tino\Payment\Api\Data;

interface InvoiceItemInterface
{
    const ID = 'id';
    const ITEM_ID = 'item_id';
    const PARENT_ID = 'parent_id';
    const QTY = 'qty';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    /**
     * @return int
     */
    public function getId();

    /**
     * @return int
     */
    public function getItemId();

    /**
     * @return int
     */
    public function getParentId();

    /**
     * @return int
     */
    public function getQty();

    /**
     * @return string
     */
    public function getCreatedAt();

    /**
     * @return string
     */
    public function getUpdatedAt();

    /**
     * @param $id
     * @return int
     */
    public function setId($id);

    /**
     * @param $itemId
     * @return int
     */
    public function setItemId($itemId);

    /**
     * @param $parentId
     * @return int
     */
    public function setParentId($parentId);

    /**
     * @param $qty
     * @return int
     */
    public function setQty($qty);

    /**
     * @param $createdAt
     * @return string
     */
    public function setCreatedAt($createdAt);

    /**
     * @param $updatedAt
     * @return string
     */
    public function setUpdatedAt($updatedAt);
}

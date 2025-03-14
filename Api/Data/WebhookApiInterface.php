<?php

namespace Tino\Payment\Api\Data;

use Tino\Payment\Api\Data\WebhookApiDataInterface;

interface WebhookApiInterface
{
    const TYPE = 'type';

    /**
     * Get the webhook type.
     *
     * @return string
     */
    public function getType();

    /**
     * Set the webhook type.
     *
     * @param string $type
     * @return void
     */
    public function setType($type);
}

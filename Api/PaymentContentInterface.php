<?php

namespace Tino\Payment\Api;

interface PaymentContentInterface
{
    /**
     * Get banner, logo and title
     *
     * @param $clientProfileData
     *
     * @return string
     */
    public function getPaymentContent($clientProfileData): string;
}

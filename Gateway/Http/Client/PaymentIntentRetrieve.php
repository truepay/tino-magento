<?php

declare(strict_types=1);

namespace Tino\Payment\Gateway\Http\Client;

use Magento\Payment\Gateway\Http\ClientInterface;
use Magento\Payment\Gateway\Http\TransferInterface;

class PaymentIntentRetrieve implements ClientInterface
{
    public function placeRequest(TransferInterface $transferObject)
    {
        return [];
    }
}

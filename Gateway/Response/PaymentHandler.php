<?php

namespace Tino\Payment\Gateway\Response;

use Magento\Payment\Gateway\Response\HandlerInterface;

class PaymentHandler implements HandlerInterface
{
    public function handle(array $handlingSubject, array $response): void
    {
    }
}

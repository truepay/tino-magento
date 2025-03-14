<?php

declare(strict_types=1);

namespace Tino\Payment\Gateway\Response;

use Magento\Payment\Gateway\Response\HandlerInterface;

class CaptureHandler implements HandlerInterface
{
    public function handle(array $handlingSubject, array $response)
    {
    }
}

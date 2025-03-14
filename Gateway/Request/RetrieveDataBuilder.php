<?php

namespace Tino\Payment\Gateway\Request;

use Magento\Payment\Gateway\Request\BuilderInterface;
use Magento\Payment\Gateway\Helper;
use Tino\Payment\Observer\DataAssignObserver;

class RetrieveDataBuilder  implements BuilderInterface
{
    public function build(array $buildSubject): array
    {
        return [];
    }
}

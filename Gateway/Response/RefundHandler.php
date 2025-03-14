<?php

declare(strict_types=1);

namespace Tino\Payment\Gateway\Response;

use Magento\Payment\Gateway\Response\HandlerInterface;
use Magento\Sales\Model\Order\Payment;
use Magento\Payment\Gateway\Helper;

class RefundHandler implements HandlerInterface
{
    public function handle(array $handlingSubject, array $response)
    {
        $payment = Helper\SubjectReader::readPayment($handlingSubject);

        /** @var Payment $orderPayment */
        $orderPayment = $payment->getPayment();

        $order = $orderPayment->getOrder();
        $comment = __('Order Refund in Tino API.');
        $order->addCommentToStatusHistory($comment);
    }
}

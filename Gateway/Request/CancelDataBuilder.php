<?php

declare(strict_types=1);

namespace Tino\Payment\Gateway\Request;

use Magento\Payment\Gateway\Request\BuilderInterface;
use Magento\Payment\Gateway\Helper;
use Magento\Framework\Exception\LocalizedException;

class CancelDataBuilder implements BuilderInterface
{

    /**
     * @throws LocalizedException
     */
    public function build(array $buildSubject): array
    {
        $payment = Helper\SubjectReader::readPayment($buildSubject);
        $reservationId = $payment->getPayment()->getAdditionalInformation('reservation_id');

        if (!$reservationId) {
            throw new LocalizedException(__('Not found "Payment Reservation Id" to proceed cancel.'));
        }

        return [
            "reservation_id" => $reservationId
        ];
    }
}

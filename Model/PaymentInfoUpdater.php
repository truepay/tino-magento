<?php

declare(strict_types=1);

namespace Tino\Payment\Model;

use Magento\Framework\Exception\LocalizedException;
use Tino\Payment\Api\ApiInterface;
use Magento\Sales\Api\OrderPaymentRepositoryInterface;
use Magento\Sales\Model\Order\Payment;
use Magento\Payment\Model\Method\Logger;

class PaymentInfoUpdater
{
    const EXTERNAL_ID = 'external_id';
    const AMOUNT_CENTS = 'amount_cents';
    const CASH_OUT_FREQUENCY = 'cash_out_frequency';

    private ApiInterface $api;
    private Logger $logger;
    private OrderPaymentRepositoryInterface $paymentRepository;

    public function __construct(
        ApiInterface $api,
        Logger $logger,
        OrderPaymentRepositoryInterface $paymentRepository
    ) {
        $this->api = $api;
        $this->logger = $logger;
        $this->paymentRepository = $paymentRepository;
    }

    /**
     * Update additional payment information if necessary.
     *
     * @param Payment $payment
     * @throws LocalizedException
     */
    public function updatePaymentInfo(Payment $payment): void
    {
        $externalId = $payment->getAdditionalInformation(self::EXTERNAL_ID);
        if (!$externalId) {
            return;
        }

        $amountCents = $payment->getAdditionalInformation(self::AMOUNT_CENTS);
        $cashOutFrequency = $payment->getAdditionalInformation(self::CASH_OUT_FREQUENCY);

        if (!$amountCents || !$cashOutFrequency) {
            $infoApi = $this->api->getLimitReservation($externalId);

            $latestReservation = $infoApi['response']['limitReservations'][0] ?? null;

            if ($latestReservation) {
                $payment->setAdditionalInformation(self::AMOUNT_CENTS, $latestReservation['amountCents'] ?? null);
                $payment->setAdditionalInformation(self::CASH_OUT_FREQUENCY, $latestReservation['cashoutFrequency'] ?? null);

                $grandTotal = (float) $payment->getOrder()->getGrandTotal();
                $amountCents = (float) ($latestReservation['amountCents'] ?? 0) / 100;
                $tax = $grandTotal - $amountCents;

                $payment->setAdditionalInformation('tax', $tax);

                try {
                    $this->paymentRepository->save($payment);
                } catch (\Exception $e) {
                    $this->logger->debug(
                        [
                            'error' => $e->getCode(),
                            'message' => $e->getMessage()
                        ]
                    );
                }
            }
        }
    }
}

<?php

declare(strict_types=1);

namespace Tino\Payment\Block;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Element\Template\Context;
use Magento\Payment\Block\ConfigurableInfo;
use Magento\Payment\Gateway\ConfigInterface;
use Tino\Payment\Model\PaymentInfoUpdater;
use Magento\Sales\Model\Order\Payment;
use Magento\Framework\Pricing\Helper\Data as PricingHelper;

class Info extends ConfigurableInfo
{
    protected $_template = 'Tino_Payment::info/details.phtml';

    private PaymentInfoUpdater $paymentInfoUpdater;

    public function __construct(
        Context $context,
        ConfigInterface $config,
        PaymentInfoUpdater $paymentInfoUpdater,
        PricingHelper $pricingHelper,
        array $data = []
    ) {
        parent::__construct($context, $config, $data);
        $this->paymentInfoUpdater = $paymentInfoUpdater;
        $this->pricingHelper = $pricingHelper;
    }

    /**
     * @return mixed
     * @throws LocalizedException
     */
    public function getReservationId(): mixed
    {
        return $this->getInfo()->getAdditionalInformation('reservation_id');
    }

    /**
     * @return string
     * @throws LocalizedException
     */
    public function getFrequency(): string
    {
        $additionalInfo = $this->getInfoReservation();
        if (isset($additionalInfo['cash_out_frequency'])){
            return implode('/', $additionalInfo['cash_out_frequency']);
        }
        return "";
    }

    /**
     * @return string
     * @throws LocalizedException
     */
    public function getTax(): string
    {
        $additionalInfo = $this->getInfoReservation();
        if (isset($additionalInfo['tax'])){
            $tax = $additionalInfo['tax'];
            return $tax > 0 ? $this->pricingHelper->currency($additionalInfo['tax']) : "";
        }

        return "";
    }

    /**
     * @return mixed
     * @throws LocalizedException
     */
    private function getInfoReservation(): mixed
    {
        $payment = $this->getInfo();
        if ($payment instanceof Payment) {
            $this->paymentInfoUpdater->updatePaymentInfo($payment);
        }

        return $payment->getAdditionalInformation();
    }
}

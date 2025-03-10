<?php

declare(strict_types=1);

namespace Tino\Payment\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

class AttributeCnpj implements OptionSourceInterface
{
    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'vat_id', 'label' => __('VAT_ID (Shipping Address)')],
            ['value' => 'taxvat', 'label' => __('TAXVAT (Customer)')]
        ];
    }
}

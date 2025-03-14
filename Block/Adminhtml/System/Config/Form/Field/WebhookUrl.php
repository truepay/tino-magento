<?php

declare(strict_types=1);

namespace Tino\Payment\Block\Adminhtml\System\Config\Form\Field;

use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;

class WebhookUrl extends Field
{
    /**
     * @param AbstractElement $element
     * @return string
     * @throws \Exception
     */
    protected function _renderValue(AbstractElement $element)
    {
        $html = '<td class="value">';
        $html .= $this->_getElementHtml($element);
        $html .= "<p><span>{$this->getBaseUrl()}rest/V1/tino/webhook</span></p>";
        $html .= '</td>';

        return $html;
    }
}

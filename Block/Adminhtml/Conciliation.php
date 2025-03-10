<?php

declare(strict_types=1);

namespace Tino\Payment\Block\Adminhtml;

use Magento\Backend\Block\Template;

class Conciliation extends Template
{

    /**
     * @return string
     */
    public function getAjaxUrl(): string
    {
        return $this->getUrl('tino/conciliation/downloadconciliation');
    }
}

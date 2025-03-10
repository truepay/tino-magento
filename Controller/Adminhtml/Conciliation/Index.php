<?php

declare(strict_types=1);

namespace Tino\Payment\Controller\Adminhtml\Conciliation;

use Magento\Framework\App\ActionInterface;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;

class Index implements ActionInterface
{
    const ADMIN_RESOURCE = 'Tino_Payment::conciliation';

    protected PageFactory $resultPageFactory;

    public function __construct(
        PageFactory $resultPageFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
    }

    /**
     * @return Page
     */
    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->prepend(__('Tino - Financial Reconciliation'));
        return $resultPage;
    }
}

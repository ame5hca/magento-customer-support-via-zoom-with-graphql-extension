<?php

namespace AmeshExtensions\CustomerSupport\Controller\Adminhtml\Meeting;

use Magento\Backend\App\Action;
use Magento\Framework\View\Result\PageFactory;
use Magento\Backend\App\Action\Context;

class ParticipantsGrid extends Action
{
    protected $resultPageFactory;

    public function __construct(
        Context $context,        
        PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }

    public function execute()
    {
        return $this->resultPageFactory->create();
    }
}

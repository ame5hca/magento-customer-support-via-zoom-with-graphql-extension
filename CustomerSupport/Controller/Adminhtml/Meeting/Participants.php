<?php

namespace AmeshExtensions\CustomerSupport\Controller\Adminhtml\Meeting;

use Magento\Backend\App\Action;
use Magento\Framework\View\Result\LayoutFactory;
use Magento\Backend\App\Action\Context;

class Participants extends Action
{
    protected $resultLayoutFactory;

    public function __construct(
        Context $context,
        LayoutFactory $resultLayoutFactory
    ) {
        $this->resultLayoutFactory = $resultLayoutFactory;
        parent::__construct($context);
    }
    
    public function execute()
    {
        $resultLayout = $this->resultLayoutFactory->create();
        return $resultLayout;
    }
}

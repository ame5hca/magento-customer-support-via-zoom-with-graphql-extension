<?php

namespace AmeshExtensions\CustomerSupport\Block\Adminhtml\CallbackRequests;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Backend\Model\Auth\Session as AdminSession;

class CopyUrl extends Template
{
    private $adminSession;

    public function __construct(
        Context $context,
        AdminSession $adminSession,
        array $data = []
    ) {
        $this->adminSession = $adminSession;
        parent::__construct($context, $data);
    }

    public function getCurrentAdminId()
    {
        $adminUser = $this->adminSession->getUser();
        return $adminUser->getId();
    }

    public function getSupportUrl()
    {
        $adminId = $this->getCurrentAdminId();
        return $this->getBaseUrl().'video-support?agent_id='.$adminId;
    }
}
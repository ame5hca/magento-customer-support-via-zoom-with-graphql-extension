<?php

namespace AmeshExtensions\CustomerSupport\Block\Adminhtml\CallbackRequests\Edit\Tab\View;

use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;
use AmeshExtensions\CustomerSupport\Model\ServiceProvider\Registry\CallbackRequestRegistry;
use AmeshExtensions\CustomerSupport\Model\ResourceModel\MeetingCallbackRequests;

class Info extends Template
{
    private $callbackRequestRegistry;

    public function __construct(
        Context $context,
        CallbackRequestRegistry $callbackRequestRegistry,
        array $data = []
    ) {
        $this->callbackRequestRegistry = $callbackRequestRegistry;
        parent::__construct($context, $data);
    }

    public function getCallbackRequestInfo()
    {
        $callbackRequestInfo = $this->callbackRequestRegistry->registry(
            MeetingCallbackRequests::CALLBACK_REQUEST_REGISTRY_ID
        );
        if (!$callbackRequestInfo) {
            return null;
        }

        return $callbackRequestInfo;
    }
}

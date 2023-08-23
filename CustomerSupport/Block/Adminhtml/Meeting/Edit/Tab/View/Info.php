<?php

namespace AmeshExtensions\CustomerSupport\Block\Adminhtml\Meeting\Edit\Tab\View;

use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;
use AmeshExtensions\CustomerSupport\Model\ServiceProvider\Registry\MeetingRegistry;
use AmeshExtensions\CustomerSupport\Model\ResourceModel\MeetingList as MeetingResourceModel;

class Info extends Template
{
    private $meetingRegistry;

    public function __construct(
        Context $context,
        MeetingRegistry $meetingRegistry,
        array $data = []
    ) {
        $this->meetingRegistry = $meetingRegistry;
        parent::__construct($context, $data);
    }

    public function getMeetingInfo()
    {
        $meetingInfo = $this->meetingRegistry->registry(
            MeetingResourceModel::MEETING_REGISTRY_ID
        );
        if (!$meetingInfo) {
            return null;
        }

        return $meetingInfo;
    }
}
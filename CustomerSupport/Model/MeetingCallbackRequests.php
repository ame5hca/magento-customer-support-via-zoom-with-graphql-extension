<?php

namespace AmeshExtensions\CustomerSupport\Model;

use Magento\Framework\Model\AbstractModel;
use AmeshExtensions\CustomerSupport\Model\ResourceModel\MeetingCallbackRequests as MeetingCallbackRequestsResourceModel;

class MeetingCallbackRequests extends AbstractModel
{
    protected function _construct()
    {
        $this->_init(MeetingCallbackRequestsResourceModel::class);
    }
}

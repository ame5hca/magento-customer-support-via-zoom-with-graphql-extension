<?php

namespace AmeshExtensions\CustomerSupport\Model\ResourceModel\MeetingCallbackRequests;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use AmeshExtensions\CustomerSupport\Model\MeetingCallbackRequests;
use AmeshExtensions\CustomerSupport\Model\ResourceModel\MeetingCallbackRequests as MeetingCallbackRequestsResourceModel;

class Collection extends AbstractCollection
{
    protected function _construct()
    {
        $this->_init(
            MeetingCallbackRequests::class, 
            MeetingCallbackRequestsResourceModel::class
        );
    }
}

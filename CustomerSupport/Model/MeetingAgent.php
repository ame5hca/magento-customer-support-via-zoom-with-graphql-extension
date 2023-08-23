<?php

namespace AmeshExtensions\CustomerSupport\Model;

use Magento\Framework\Model\AbstractModel;
use AmeshExtensions\CustomerSupport\Model\ResourceModel\MeetingAgent as MeetingAgentResourceModel;

class MeetingAgent extends AbstractModel
{
    protected function _construct()
    {
        $this->_init(MeetingAgentResourceModel::class);
    }
}

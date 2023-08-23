<?php

namespace AmeshExtensions\CustomerSupport\Model\ResourceModel\MeetingAgent;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use AmeshExtensions\CustomerSupport\Model\MeetingAgent;
use AmeshExtensions\CustomerSupport\Model\ResourceModel\MeetingAgent as MeetingAgentResourceModel;

class Collection extends AbstractCollection
{
    protected function _construct()
    {
        $this->_init(MeetingAgent::class, MeetingAgentResourceModel::class);
    }
}

<?php

namespace AmeshExtensions\CustomerSupport\Model;

use Magento\Framework\Model\AbstractModel;
use AmeshExtensions\CustomerSupport\Model\ResourceModel\MeetingList as MeetingListResourceModel;

class MeetingList extends AbstractModel
{
    protected function _construct()
    {
        $this->_init(MeetingListResourceModel::class);
    }
}

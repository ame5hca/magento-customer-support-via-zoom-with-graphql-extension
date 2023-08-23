<?php

namespace AmeshExtensions\CustomerSupport\Model\ResourceModel\MeetingList;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use AmeshExtensions\CustomerSupport\Model\MeetingList;
use AmeshExtensions\CustomerSupport\Model\ResourceModel\MeetingList as MeetingListResourceModel;

class Collection extends AbstractCollection
{
    protected function _construct()
    {
        $this->_init(MeetingList::class, MeetingListResourceModel::class);
    }
}

<?php

namespace AmeshExtensions\CustomerSupport\Model\ResourceModel\MeetingParticipant;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use AmeshExtensions\CustomerSupport\Model\MeetingParticipant;
use AmeshExtensions\CustomerSupport\Model\ResourceModel\MeetingParticipant as MeetingParticipantResourceModel;

class Collection extends AbstractCollection
{
    protected function _construct()
    {
        $this->_init(MeetingParticipant::class, MeetingParticipantResourceModel::class);
    }
}

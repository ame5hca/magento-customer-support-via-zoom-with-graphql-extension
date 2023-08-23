<?php

namespace AmeshExtensions\CustomerSupport\Model;

use Magento\Framework\Model\AbstractModel;
use AmeshExtensions\CustomerSupport\Model\ResourceModel\MeetingParticipant as MeetingParticipantResourceModel;

class MeetingParticipant extends AbstractModel
{
    protected function _construct()
    {
        $this->_init(MeetingParticipantResourceModel::class);
    }
}

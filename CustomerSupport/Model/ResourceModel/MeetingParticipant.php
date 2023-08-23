<?php

namespace AmeshExtensions\CustomerSupport\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class MeetingParticipant extends AbstractDb
{
    const MEETING_PARTICIPANT_REGISTRY_ID = 'participant_info';
    
    protected function _construct()
    {
        $this->_init('customersupport_meeting_participant', 'entity_id');
    }
}

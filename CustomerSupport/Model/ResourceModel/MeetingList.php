<?php

namespace AmeshExtensions\CustomerSupport\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class MeetingList extends AbstractDb
{
    const MEETING_REGISTRY_ID = 'meeting_info';

    protected function _construct()
    {
        $this->_init('customersupport_meeting', 'entity_id');
    }
}

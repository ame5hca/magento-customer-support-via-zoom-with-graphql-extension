<?php

namespace AmeshExtensions\CustomerSupport\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class MeetingAgent extends AbstractDb
{
    protected function _construct()
    {
        $this->_init('customersupport_agent', 'entity_id');
    }
}

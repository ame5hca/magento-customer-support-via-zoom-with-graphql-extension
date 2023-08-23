<?php

namespace AmeshExtensions\CustomerSupport\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class MeetingCallbackRequests extends AbstractDb
{
    const CALLBACK_REQUEST_REGISTRY_ID = 'callback_requests';
    
    protected function _construct()
    {
        $this->_init('customersupport_callback_requests', 'entity_id');
    }
}

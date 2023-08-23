<?php

namespace AmeshExtensions\CustomerSupport\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class ZoomHostUsers extends AbstractDb
{
    protected function _construct()
    {
        $this->_init('zoom_host_users', 'entity_id');
    }
}

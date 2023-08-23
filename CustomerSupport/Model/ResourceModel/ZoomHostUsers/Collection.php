<?php

namespace AmeshExtensions\CustomerSupport\Model\ResourceModel\ZoomHostUsers;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use AmeshExtensions\CustomerSupport\Model\ZoomHostUsers;
use AmeshExtensions\CustomerSupport\Model\ResourceModel\ZoomHostUsers as ZoomHostUsersResourceModel;

class Collection extends AbstractCollection
{
    protected function _construct()
    {
        $this->_init(ZoomHostUsers::class, ZoomHostUsersResourceModel::class);
    }
}

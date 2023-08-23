<?php

namespace AmeshExtensions\CustomerSupport\Model;

use Magento\Framework\Model\AbstractModel;
use AmeshExtensions\CustomerSupport\Model\ResourceModel\ZoomHostUsers as ZoomHostUsersResourceModel;

class ZoomHostUsers extends AbstractModel
{
    protected function _construct()
    {
        $this->_init(ZoomHostUsersResourceModel::class);
    }
}

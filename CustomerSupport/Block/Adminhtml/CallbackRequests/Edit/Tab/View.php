<?php

namespace AmeshExtensions\CustomerSupport\Block\Adminhtml\CallbackRequests\Edit\Tab;

use Magento\Ui\Component\Layout\Tabs\TabInterface;
use Magento\Backend\Block\Template;

/**
 * Callback request info
 */
class View extends Template implements TabInterface
{
    /**
     * @return \Magento\Framework\Phrase
     */
    public function getTabLabel()
    {
        return __('Request Info');
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __('Request Info');
    }

    /**
     * @return bool
     */
    public function canShowTab()
    {        
        return true;
    }

    /**
     * @return bool
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Tab class getter
     *
     * @return string
     */
    public function getTabClass()
    {
        return '';
    }

    /**
     * Return URL link to Tab content
     *
     * @return string
     */
    public function getTabUrl()
    {
        return '';
    }

    /**
     * Tab should be loaded trough Ajax call
     *
     * @return bool
     */
    public function isAjaxLoaded()
    {
        return false;
    }
}

<?php

namespace AmeshExtensions\CustomerSupport\Block\Adminhtml\Meeting\Edit;

use Magento\Ui\Component\Layout\Tabs\TabWrapper;

class ParticipantsTab extends TabWrapper
{
    /**
     * @var bool
     */
    protected $isAjaxLoaded = true;
    
    /**
     * @inheritdoc
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Return Tab label
     *    
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabLabel()
    {
        return __('Participants');
    }

    /**
     * Return URL link to Tab content
     *
     * @return string
     */
    public function getTabUrl()
    {
        return $this->getUrl('customersupport/meeting/participants', ['_current' => true]);
    }
}

<?php

namespace AmeshExtensions\CustomerSupport\Block\Adminhtml\Meeting\Edit;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class CreateMeetingButton extends GenericButton implements ButtonProviderInterface
{
    /**
     * @return array
     */
    public function getButtonData()
    {
        return [
            'label' => __('Create Meeting'),
            'class' => 'save primary',
            'data_attribute' => [
                'mage-init' => ['button' => ['event' => 'save']],
                'form-role' => 'save',
            ],
            'sort_order' => 90,
        ];
    }
}

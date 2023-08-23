<?php

namespace AmeshExtensions\CustomerSupport\Block\Adminhtml\Meeting\Edit;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;
use AmeshExtensions\CustomerSupport\Model\ServiceProvider\Registry\MeetingRegistry;
use AmeshExtensions\CustomerSupport\Model\ResourceModel\MeetingList as MeetingResourceModel;
use Magento\Backend\Block\Widget\Context;

class ManageMeetingButton extends GenericButton implements ButtonProviderInterface
{
    private $meetingRegistry;

    public function __construct(
        Context $context,
        MeetingRegistry $meetingRegistry
    ) {
        $this->meetingRegistry = $meetingRegistry;       
        parent::__construct($context);
    }

    /**
     * @return array
     */
    public function getButtonData()
    {
        $data = [];
        $meeting = $this->meetingRegistry->registry(
            MeetingResourceModel::MEETING_REGISTRY_ID
        );
        if ($this->getMeetingId()) {
            if ($meeting->getStatus() == 'waiting') {
                $data = [
                    'label' => __('Host Meeting'),
                    'class' => 'save primary',
                    'target' => '_blank',
                    'on_click' => 'window.open(
                        \'' . $meeting->getStartUrl() . '\'
                    )',
                    'sort_order' => 20,
                ];
            } else {
                $data = [
                    'label' => __('Stop Meeting'),
                    'class' => 'delete',
                    'on_click' => 'deleteConfirm(\'' . __(
                        'Are you sure you want to do this?'
                    ) . '\', \'' . $this->getStopMeetingUrl() . '\')',
                    'sort_order' => 20,
                ];
            }            
        }
        return $data;
    }

    /**
     * Get URL for stop meeting button
     *
     * @return string
     */
    public function getStopMeetingUrl()
    {
        return $this->getUrl('*/*/stopmeeting', ['id' => $this->getMeetingId()]);
    }
}

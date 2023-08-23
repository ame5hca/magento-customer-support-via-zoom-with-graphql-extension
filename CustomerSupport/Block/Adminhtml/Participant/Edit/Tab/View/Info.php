<?php

namespace AmeshExtensions\CustomerSupport\Block\Adminhtml\Participant\Edit\Tab\View;

use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;
use AmeshExtensions\CustomerSupport\Model\ServiceProvider\Registry\ParticipantRegistry;
use AmeshExtensions\CustomerSupport\Model\ResourceModel\MeetingParticipant;

class Info extends Template
{
    private $participantRegistry;

    public function __construct(
        Context $context,
        ParticipantRegistry $participantRegistry,
        array $data = []
    ) {
        $this->participantRegistry = $participantRegistry;
        parent::__construct($context, $data);
    }

    public function getMeetingParticipantInfo()
    {
        $meetingParticipantInfo = $this->participantRegistry->registry(
            MeetingParticipant::MEETING_PARTICIPANT_REGISTRY_ID
        );
        if (!$meetingParticipantInfo) {
            return null;
        }

        return $meetingParticipantInfo;
    }
}
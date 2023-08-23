<?php

namespace AmeshExtensions\CustomerSupportGraphQl\Model\Zoom;

use AmeshExtensions\CustomerSupport\Model\ResourceModel\MeetingAgent\CollectionFactory;
use AmeshExtensions\CustomerSupport\Model\MeetingManager;
use AmeshExtensions\CustomerSupport\Model\MeetingParticipantManager;
use Magento\Framework\Exception\LocalizedException;

class GetZoomMeeting
{
    private $meetingAgentCollectionFactory;

    private $meetingParticipantManager;

    private $meetingManager;

    public function __construct(
        CollectionFactory $meetingAgentCollectionFactory,
        MeetingParticipantManager $meetingParticipantManager,
        MeetingManager $meetingManager
    ) {
        $this->meetingAgentCollectionFactory = $meetingAgentCollectionFactory;
        $this->meetingParticipantManager = $meetingParticipantManager;
        $this->meetingManager = $meetingManager;
    }

    public function getMeeting($customerId, $inputArgs)
    {
        try {
            $categoryAgentCollection = $this->getCategoryAgents(
                $inputArgs['category_id']
            );
            if (!$categoryAgentCollection) {
                throw new LocalizedException(
                    __('No agents are now availabe to support in this category.')
                );
            }
            $meeting = null;
            if (!empty($inputArgs['agent_id'])) {
                $meeting = $this->meetingManager->getActiveMeetingForAgent(
                    $inputArgs['agent_id']
                );
            } else {
                foreach ($categoryAgentCollection as $agent) {
                    $meeting = $this->meetingManager->getActiveMeetingForAgent(
                        $agent->getAdminId()
                    );
                    if ($meeting != null) {
                        break;
                    }
                }
            }
            if ($meeting == null) {
                return [
                    'zoom_link' => '',
                    'zoom_link_desktop' => '',
                    'zoom_link_mobile' => '',
                    'show_callback' => true
                ];
            }
            $meetingId = $meeting->getMeetingId();
            $joinUrl = $this->meetingManager->getDirectBrowserJoinUrl($meeting);
            $desktopAppJoinUrl = $this->meetingManager->getDesktopAppJoinUrl($meeting);
            $mobileAppJoinUrl = $this->meetingManager->getMobileAppJoinUrl($meeting);
            $updatedTopic = 'Support-' . $meeting->getMeetingId() . '-' . $customerId;
            $this->meetingManager->updateMeeting(
                $meeting,
                ['topic' => $updatedTopic]
            );
            $this->meetingParticipantManager->addNewparticipantToMeeting(
                $this->prepareParticipantData($meeting, $customerId, $inputArgs)
            );
            $this->meetingManager->reserveMeeting($meetingId);

            return [
                'zoom_link' => $joinUrl,
                'zoom_link_desktop' => $desktopAppJoinUrl,
                'zoom_link_mobile' => $mobileAppJoinUrl,
                'show_callback' => false
            ];
        } catch (\Exception $e) {
            throw new LocalizedException(
                __($e->getMessage())
            );
        }
    }

    private function getCategoryAgents($categoryId)
    {
        $meetingAgentCollection = $this->meetingAgentCollectionFactory->create();
        $meetingAgentCollection->addFieldToFilter(

            ['primary_category_id', 'secondary_category_id'],
            [
                ['eq' => $categoryId],
                ['eq' => $categoryId]
            ]

        );
        $meetingAgentCollection->addFieldToFilter(
            'is_agent',
            ['eq' => 1]
        );
        if ($meetingAgentCollection->getSize() > 0) {
            return $meetingAgentCollection;
        }

        return false;
    }

    private function prepareParticipantData($meeting, $customerId, $inputArgs)
    {
        return [
            'meeting_id' => $meeting->getId(),
            'participant_email' => $inputArgs['customer_email'],
            'status' => 'waiting',
            'customer_id' => $customerId
        ];
    }
}

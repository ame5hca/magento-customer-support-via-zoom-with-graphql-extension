<?php

namespace AmeshExtensions\CustomerSupport\Model;

use AmeshExtensions\CustomerSupport\Logger\Zoom\Logger;
use Magento\Framework\Exception\LocalizedException;
use AmeshExtensions\CustomerSupport\Model\MeetingListFactory;
use AmeshExtensions\CustomerSupport\Model\ResourceModel\MeetingParticipant\CollectionFactory;
use AmeshExtensions\CustomerSupport\Model\ResourceModel\MeetingList\CollectionFactory as MeetingListCollectionFactory;
use AmeshExtensions\CustomerSupport\Model\Zoom\MeetingApi;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use AmeshExtensions\CustomerSupport\Model\MeetingList;

class MeetingManager
{
    const ZOOM_BASE_URL = 'https://zoom.us/';

    private $meetingFactory;

    private $participantsCollectionFactory;

    private $meetingListCollectionFactory;

    private $meetingApi;

    private $logger;

    private $datetime;

    public function __construct(
        MeetingListFactory $meetingFactory,
        CollectionFactory $participantsCollectionFactory,
        MeetingListCollectionFactory $meetingListCollectionFactory,
        MeetingApi $meetingApi,
        TimezoneInterface $datetime,
        Logger $logger
    ) {
        $this->participantsCollectionFactory = $participantsCollectionFactory;
        $this->meetingListCollectionFactory = $meetingListCollectionFactory;
        $this->meetingFactory = $meetingFactory;
        $this->meetingApi = $meetingApi;
        $this->datetime = $datetime;
        $this->logger = $logger;
    }

    public function saveMeetingInfo($data, $mid = null)
    {
        $meetingModel = $this->meetingFactory->create();
        if (!empty($mid)) {
            $meetingModel = $meetingModel->load($mid);
            if (!$meetingModel->getId()) {
                throw new LocalizedException(
                    __('No meeting exist with this id.')
                );
            }
        }
        $meetingModel->setAgentId($data['agent_id']);
        $meetingModel->setMeetingId($data['id']);
        $meetingModel->setHostId($data['host_id']);
        $meetingModel->setTopic($data['topic']);
        $meetingModel->setMeetingType($data['type']);
        $meetingModel->setStartTime($data['start_time']);
        if (isset($data['end_time'])) {
            $meetingModel->setEndTime($data['end_time']);
        }
        $meetingModel->setStatus($data['status']);
        if (isset($data['end_time'])) {
            $meetingModel->setDuration($data['duration']);
        }
        $meetingModel->setStartUrl($data['start_url']);
        $meetingModel->setJoinUrl($data['join_url']);
        if (isset($data['password'])) {
            $meetingModel->setPassword($data['password']);
        }
        if (isset($data['encrypted_password'])) {
            $meetingModel->setEncryptedPassword($data['encrypted_password']);
        }
        $meetingModel->save();

        $this->endPreviousMeetingsOfAgent($meetingModel->getId(), $data['agent_id']);

        return $meetingModel->getId();
    }

    public function endPreviousMeetingsOfAgent($meetingId, $agentId)
    {
        $meetingListCollection = $this->meetingListCollectionFactory->create();
        $meetingListCollection->addFieldToFilter('entity_id', ['neq' => $meetingId]);
        $meetingListCollection->addFieldToFilter('agent_id', ['eq' => $agentId]);
        if ($meetingListCollection->getSize() > 0) {
            foreach ($meetingListCollection as $meeting) {
                $meeting->setStatus('ended');
                $meeting->save();
            }
        }
    }

    public function addParticipantToMeeting($data)
    {
        $eventData = $data['payload']['object'];
        $meetingModel = $this->meetingFactory->create();
        $meeting = $meetingModel->load($eventData['id'], 'meeting_id');
        if (!$meeting->getId()) {
            return;
        }
        if ($meeting->getHostId() != $eventData['host_id']) {
            return;
        }
        $meetingTopicParts = explode("-", $eventData['topic']);
        if (count($meetingTopicParts) != 3) {
            throw new LocalizedException(
                __(
                    'AddParticipantToMeetingError:
                     Unable to fetch user info from topic. MeetingId=' . $eventData['id'] . '
                     ZoomUserId=' . $eventData['participant']['user_id']
                )
            );
        }
        $participantCollection = $this->participantsCollectionFactory->create();
        $participantCollection->addFieldToFilter(
            'meeting_id',
            ['eq' => $meeting->getId()]
        );
        $participantCollection->addFieldToFilter(
            'customer_id',
            ['eq' => $meetingTopicParts[2]]
        );
        $todayStart = $this->datetime->date()->format('Y-m-d 00:00:00');
        $todayEnd = $this->datetime->date()->format('Y-m-d 23:59:59');
        $participantCollection->addFieldToFilter(
            'created_at',
            ['from' => $todayStart, 'to' => $todayEnd]
        );
        if ($participantCollection->getSize() <= 0) {
            throw new LocalizedException(
                __(
                    'AddParticipantToMeetingError:
                     No participant is in waiting state. MeetingId=' . $eventData['id'] . '
                     ZoomUserId=' . $eventData['participant']['user_id']
                )
            );
        }
        $participant = $participantCollection->getFirstItem();
        if ($participant->getStatus() != 'waiting') {
            $participant->setStatus('rejoined');
        } else {
            $participant->setStatus('joined');
        }
        $participant->setParticipantName($eventData['participant']['user_name']);
        $participant->setJoinId($eventData['participant']['id']);
        $participant->setParticipantZoomId($eventData['participant']['user_id']);
        $participant->setJoinTime($eventData['participant']['join_time']);
        if ($participant->save()) {
            $meeting->setStatus('inprogress');
            $meeting->setReservationAt('');
            $meeting->save();
        }
    }

    public function removeParticipantFromMeeting($data)
    {
        $eventData = $data['payload']['object'];
        $meetingModel = $this->meetingFactory->create();
        $meeting = $meetingModel->load($eventData['id'], 'meeting_id');
        if (!$meeting->getId()) {
            return;
        }
        if ($meeting->getHostId() != $eventData['host_id']) {
            return;
        }
        $participantCollection = $this->participantsCollectionFactory->create();
        $participantCollection->addFieldToFilter(
            'meeting_id',
            ['eq' => $meeting->getId()]
        );
        $participantCollection->addFieldToFilter(
            'participant_zoom_id',
            ['eq' => $eventData['participant']['user_id']]
        );
        $participantCollection->addFieldToFilter(
            'status',
            ['in' => ['joined', 'rejoined']]
        );
        if ($participantCollection->getSize() <= 0) {
            throw new LocalizedException(
                __(
                    'RemoveParticipantFromMeeting:
                     No user is in joined oe rejoined state. MeetingId=' . $eventData['id'] . '
                     ZoomUserId=' . $eventData['participant']['user_id']
                )
            );
        }
        $participant = $participantCollection->getFirstItem();

        $duration = $this->calculateDuration(
            $eventData['participant']['leave_time'],
            $participant->getJoinTime()
        );
        $participant->setLeftTime($eventData['participant']['leave_time']);
        if ($participant->getStatus() == 'rejoined') {
            $duration += $participant->getDuration();
        }
        $participant->setDuration($duration);
        $participant->setStatus('left');
        if ($participant->save()) {
            $meeting->setStatus('started');
            $meeting->save();
        }
    }

    public function startMeeting($data)
    {
        $eventData = $data['payload']['object'];
        $meetingModel = $this->meetingFactory->create();
        $meeting = $meetingModel->load($eventData['id'], 'meeting_id');
        if (!$meeting->getId()) {
            return;
        }
        if ($meeting->getHostId() != $eventData['host_id']) {
            return;
        }
        $meeting->setStatus('started');
        $meeting->save();
    }

    public function stopMeeting($data)
    {
        if (is_array($data)) {
            return $this->stopMeetingByWebhookData($data);
        }
        return $this->stopMeetingByAgent($data);
    }

    private function stopMeetingByAgent($meetingId)
    {
        $meeting = $meetingId;
        if (!($meetingId instanceof MeetingList)) {
            $meetingModel = $this->meetingFactory->create();
            $meeting = $meetingModel->load($meetingId, 'meeting_id');
        }        
        if (!$meeting->getId()) {
            return;
        }
        $meetingEndTime = date('Y-m-d H:i:s');
        $meeting->setEndTime($meetingEndTime);
        $meeting->setDuration(
            $this->calculateDuration($meetingEndTime, $meeting->getStartTime())
        );
        $meeting->setStatus('ended');
        $meeting->setReservationAt('');
        $meeting->save();
    }

    private function stopMeetingByWebhookData($data)
    {
        $eventData = $data['payload']['object'];
        $meetingModel = $this->meetingFactory->create();
        $meeting = $meetingModel->load($eventData['id'], 'meeting_id');
        if (!$meeting->getId()) {
            return;
        }
        if ($meeting->getHostId() != $eventData['host_id']) {
            return;
        }
        $meeting->setEndTime($eventData['end_time']);
        $meeting->setDuration(
            $this->calculateDuration($eventData['end_time'], $eventData['start_time'])
        );
        $meeting->setStatus('ended');
        $meeting->setReservationAt('');
        $meeting->save();
    }

    public function getActiveMeetingForAgent($agentId)
    {
        $meetingListCollection = $this->meetingListCollectionFactory->create();
        $meetingListCollection->addFieldToFilter(
            'agent_id',
            ['eq' => $agentId]
        );
        $meetingListCollection->addFieldToFilter(
            'status',
            ['eq' => 'started']
        );
        if ($meetingListCollection->getSize() > 0) {
            return $meetingListCollection->getFirstItem();
        }
        return null;
    }

    public function updateMeeting($meeting, $data)
    {
        $statusCode = $this->meetingApi->updateMeeting(
            $meeting->getMeetingId(),
            $data
        );
        if ($statusCode != 204) {
            throw new LocalizedException(
                __('Failed to update the meeting.')
            );
            $this->logger->info(
                'UpdateMeetingError: MeetingId=' . $meeting->getMeetingId() . ' StatusCode=' . $statusCode
            );
        }
        $data['entity_id'] = $meeting->getId();
        $meeting->setData($data);
        $meeting->save();
    }

    public function reserveMeeting($meetingId)
    {
        $meetingListCollection = $this->meetingListCollectionFactory->create();
        $meetingListCollection->addFieldToFilter(
            'meeting_id',
            ['eq' => $meetingId]
        );
        if ($meetingListCollection->getSize() > 0) {
            $reservationAt = $this->datetime->date()->format('Y-m-d H:i:s');
            $meeting = $meetingListCollection->getFirstItem();
            $meeting->setStatus('reserved');
            $meeting->setReservationAt($reservationAt);
            $meeting->save();
        }
    }

    public function getDirectBrowserJoinUrl($meeting)
    {
        /**
         * Currently direct browser join url is not required
         * So the default join url will be send.
         */
        return $meeting->getJoinUrl();
        $joinUrl = self::ZOOM_BASE_URL . 'wc/' . $meeting->getMeetingId() . '/join';
        $joinUrl .= '?prefer=1';
        if (!empty($meeting->getPassword())) {
            $joinUrl .= '&pwd=' . $meeting->getEncryptedPassword();
        }

        return $joinUrl;
    }

    public function getDesktopAppJoinUrl($meeting)
    {
        $zoomUrl = 'zoommtg://zoom.us/join?confno=' . $meeting->getMeetingId();
        if (!empty($meeting->getPassword())) {
            $zoomUrl .= '&pwd=' . $meeting->getEncryptedPassword();
        }
        return $zoomUrl;
    }
    
    public function getMobileAppJoinUrl($meeting)
    {
        $zoomUrl = 'zoomus://zoom.us/join?confno=' . $meeting->getMeetingId();
        if (!empty($meeting->getPassword())) {
            $zoomUrl .= '&pwd=' . $meeting->getEncryptedPassword();
        }
        return $zoomUrl;
    }

    private function calculateDuration($endTime, $startTime)
    {
        $totalSeconds = strtotime($endTime) - strtotime($startTime);
        $totalMinutes = round($totalSeconds / 60);
        return $totalMinutes;
    }
}

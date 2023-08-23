<?php

namespace AmeshExtensions\CustomerSupport\Model\Zoom;

use Magento\Framework\Webapi\Rest\Request;
use AmeshExtensions\CustomerSupport\Model\MeetingManager;
use AmeshExtensions\CustomerSupport\Logger\Zoom\Logger;
use AmeshExtensions\CustomerSupport\Model\ResourceModel\ZoomHostUsers\CollectionFactory;

class NotificationManager
{
    protected $request;

    protected $logger;

    protected $meetingManager;

    protected $meetingApi;

    private $zoomHostUsersCollectionFactory;

    private $hostingUsers = [];

    public function __construct(
        Request $request,
        DataProvider\ConfigProvider $configProvider,
        MeetingManager $meetingManager,
        Logger $logger,
        MeetingApi $meetingApi,
        CollectionFactory $zoomHostUsersCollectionFactory
    ) {
        $this->request = $request;
        $this->meetingManager = $meetingManager;
        $this->configProvider = $configProvider;
        $this->logger = $logger;
        $this->meetingApi = $meetingApi;
        $this->zoomHostUsersCollectionFactory = $zoomHostUsersCollectionFactory;
    }

    /**
     * Manage notifications
     *     
     * @return string
     */
    public function getNotifications()
    {
        $eventInfo = $this->request->getRequestData();
        $token = $this->request->getHeader('Authorization');
        $verifcnToken = $this->configProvider->getEventNotificationVerifyToken();
        if ($token != $verifcnToken) {
            return json_encode(
                ['status' => false, 'message' => 'Token mismatch']
            );
        }
        
        try {
            switch ($eventInfo['event']) {
                case 'meeting.started':
                    $this->meetingManager->startMeeting($eventInfo);
                    break;
                case 'meeting.participant_joined':
                    if (!$this->isHostingUser($eventInfo['payload']['object']['participant']['id'])) {
                        $meetingInfo = $this->meetingApi->getMeetingInfo(
                            $eventInfo['payload']['object']['id']
                        );
                        $eventInfo['payload']['object']['topic'] = $meetingInfo['topic'];
                        $this->meetingManager->addParticipantToMeeting(
                            $eventInfo
                        );
                    }                    
                    break;
                case 'meeting.participant_left':
                    $participantInfo = $eventInfo['payload']['object']['participant'];
                    if (!isset($participantInfo['id']) || !$this->isHostingUser($participantInfo['id'])) {
                        $meetingInfo = $this->meetingApi->getMeetingInfo(
                            $eventInfo['payload']['object']['id']
                        );                        
                        $eventInfo['payload']['object']['topic'] = $meetingInfo['topic'];
                        $this->meetingManager->removeParticipantFromMeeting(
                            $eventInfo
                        );
                    }                    
                    break;
                case 'meeting.ended':
                    $this->meetingManager->stopMeeting($eventInfo);
                    break;
            }
        } catch (\Exception $e) {
            $this->logger->info(__($e->getMessage()));
            return json_encode(['success' => false, 'message' => 'Failed']);
        }        

        return json_encode(['success' => true, 'message' => 'OK']);
    }

    private function isHostingUser($uid)
    {
        if (empty($uid)) {
            return false;
        }
        $zoomHostUsers = $this->zoomHostUsersCollectionFactory->create();
        $zoomHostUsers->addFieldToFilter('user_id', ['eq' => $uid]);
        if ($zoomHostUsers->getSize() > 0) {
            return true;
        }
        return false;
    }
}

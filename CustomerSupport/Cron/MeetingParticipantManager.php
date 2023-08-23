<?php

namespace AmeshExtensions\CustomerSupport\Cron;

use AmeshExtensions\CustomerSupport\Model\ResourceModel\MeetingParticipant\CollectionFactory;
use AmeshExtensions\CustomerSupport\Logger\Cron\Logger;
use \Zend_Db_Expr;

class MeetingParticipantManager
{
    private $participantCollectionFactory;

    private $logger;

    public function __construct(
        CollectionFactory $participantCollectionFactory,
        Logger $logger
    ) {
        $this->participantCollectionFactory = $participantCollectionFactory;
        $this->logger = $logger;
    }

    public function unassignParticipantFromMeeting()
    {
        try {
            $participantCollection = $this->participantCollectionFactory->create();
            $participantCollection->addFieldToFilter('status', ['eq' => 'waiting']);
            $participantCollection->getSelect()->join(
                ['meeting' => 'customersupport_meeting'],
                'main_table.meeting_id = meeting.entity_id',
                []
            );
            $participantCollection->getSelect()->where(
                new \Zend_Db_Expr('DATE(meeting.created_at) <= DATE(NOW() - INTERVAL 1 DAY)')
            );
            if ($participantCollection->getSize() > 0) {
                foreach ($participantCollection as $participant) {
                    $participant->setStatus('left');
                    if (empty($participant->getJoinTime())) {
                        $participant->setStatus('notjoined');
                    }
                    $participant->save();
                }
            }
        } catch (\Exception $e) {
            $this->logger->critical(
                'UnassignParticipantFromMeetingError: ' . $e->getMessage()
            );
        }
    }
}

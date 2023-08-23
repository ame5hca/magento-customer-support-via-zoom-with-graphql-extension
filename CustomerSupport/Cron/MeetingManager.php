<?php

namespace AmeshExtensions\CustomerSupport\Cron;

use AmeshExtensions\CustomerSupport\Model\ResourceModel\MeetingList\CollectionFactory;
use AmeshExtensions\CustomerSupport\Logger\Cron\Logger;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use \Zend_Db_Expr;

class MeetingManager
{
    const MAX_RESERVATION_TIME_IN_MINUTES = 1;

    private $meetingListCollectionFactory;

    private $datetime;

    private $logger;

    public function __construct(
        CollectionFactory $meetingListCollectionFactory,
        TimezoneInterface $datetime,
        Logger $logger
    ) {
        $this->meetingListCollectionFactory = $meetingListCollectionFactory;
        $this->datetime = $datetime;
        $this->logger = $logger;
    }

    public function expireMeetingReservation()
    {
        try {
            $currentDateTime = $this->datetime->date()->format('Y-m-d H:i:s');
            $meetingListCollection = $this->meetingListCollectionFactory->create();
            $meetingListCollection->addFieldToFilter('status', ['eq' => 'reserved']);
            $meetingListCollection->getSelect()->where(
                new \Zend_Db_Expr(
                    '"'.$currentDateTime.'" > DATE_ADD(reservation_at, INTERVAL ' . self::MAX_RESERVATION_TIME_IN_MINUTES . ' MINUTE)'
                )
            );
            if ($meetingListCollection->getSize() > 0) {
                foreach ($meetingListCollection as $meeting) {
                    $meeting->setStatus('started');
                    $meeting->setReservationAt('');
                    $meeting->save();
                }
            }
        } catch (\Exception $e) {
            $this->logger->critical(
                'ExpireMeetingReservationError: '.$e->getMessage()
            );
        }
    }

    public function endMeeting()
    {
        try {
            $meetingListCollection = $this->meetingListCollectionFactory->create();
            $meetingListCollection->addFieldToFilter('status', ['neq' => 'ended']);
            $meetingListCollection->getSelect()->where(
                new \Zend_Db_Expr('DATE(created_at) <= DATE(NOW() - INTERVAL 1 DAY)')
            );
            if ($meetingListCollection->getSize() > 0) {
                foreach ($meetingListCollection as $meeting) {
                    $meeting->setStatus('ended');
                    $meeting->setReservationAt('');
                    $meeting->save();
                }
            }
        } catch (\Exception $e) {
            $this->logger->critical(
                'EndMeetingError: '.$e->getMessage()
            );
        }
    }
}

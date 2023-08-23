<?php

namespace AmeshExtensions\CustomerSupport\Model;

use AmeshExtensions\CustomerSupport\Logger\Zoom\Logger;
use Magento\Framework\Exception\LocalizedException;
use AmeshExtensions\CustomerSupport\Model\MeetingParticipantFactory;
use AmeshExtensions\CustomerSupport\Model\ResourceModel\MeetingParticipant\CollectionFactory;

class MeetingParticipantManager
{
    private $meetingParticipantFactory;

    private $meetingParticipantCollectionFactory;

    private $logger;

    public function __construct(
        MeetingParticipantFactory $meetingParticipantFactory,
        CollectionFactory $meetingParticipantCollectionFactory,
        Logger $logger
    ) {
        $this->meetingParticipantCollectionFactory = $meetingParticipantCollectionFactory;
        $this->meetingParticipantFactory = $meetingParticipantFactory;
        $this->logger = $logger;
    }

    public function addNewparticipantToMeeting($data)
    {
        $participantCollection = $this->meetingParticipantCollectionFactory->create();
        $participantCollection->addFieldToFilter(
            'meeting_id',
            ['eq' => $data['meeting_id']]
        );
        $participantCollection->addFieldToFilter(
            'customer_id',
            ['eq' => $data['customer_id']]
        );
        /**
         * If already the user is requsted for a meeting
         * then the corresponding meeting entry will be updated
         */
        if ($participantCollection->getSize() > 0) {
            $data['entity_id'] = $participantCollection->getFirstItem()->getId();
        }
        $meetingParticipantModel = $this->meetingParticipantFactory->create();
        $meetingParticipantModel->setData($data);
        $meetingParticipantModel->save();
    }

    public function linkOrderToMeeting($orderId, $customerId)
    {
        $dateCheckFrom = $date = date("Y-m-d", strtotime("-10 day"));
        $participantCollection = $this->meetingParticipantCollectionFactory->create();
        $participantCollection->addFieldToFilter(
            'customer_id',
            ['eq' => $customerId]
        );
        $participantCollection->addFieldToFilter(
            'created_at',
            ['from' => $dateCheckFrom]
        );
        $participantCollection->setOrder('created_at', 'DESC');
        if ($participantCollection->getSize() > 0) {
            $meetingParticipant = $participantCollection->getFirstItem();
            $meetingParticipant->setOrderId($orderId);
            $meetingParticipant->save();
        }
    }
}

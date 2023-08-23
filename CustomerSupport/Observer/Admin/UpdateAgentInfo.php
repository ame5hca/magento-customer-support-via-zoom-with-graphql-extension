<?php

namespace AmeshExtensions\CustomerSupport\Observer\Admin;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\RequestInterface;
use AmeshExtensions\CustomerSupport\Model\ResourceModel\MeetingAgent\CollectionFactory;
use AmeshExtensions\CustomerSupport\Model\MeetingAgentFactory;

class UpdateAgentInfo implements ObserverInterface
{
    private $requestInterface;

    private $meetingAgentFactory;

    private $meetingAgentCollectionFactory;

    public function __construct(
        RequestInterface $requestInterface,
        CollectionFactory $meetingAgentCollectionFactory,
        MeetingAgentFactory $meetingAgentFactory
    ) {
        $this->requestInterface = $requestInterface;
        $this->meetingAgentFactory = $meetingAgentFactory;
        $this->meetingAgentCollectionFactory = $meetingAgentCollectionFactory;
    }

    public function execute(EventObserver $observer)
    {
        $user = $observer->getEvent()->getObject();
        $params = $this->requestInterface->getParams();
        if ($user->getId() && !empty($params['primary_category_id'])) {
            $meetingAgentCollection = $this->meetingAgentCollectionFactory->create();
            $meetingAgentCollection->addFieldToFilter(
                'admin_id', 
                ['eq' => $user->getId()]
            );
            if ($meetingAgentCollection->getSize() > 0) {
                $meetingAgent = $meetingAgentCollection->getFirstItem();
                $meetingAgent = $this->setMeetingData($meetingAgent, $params);
                $meetingAgent->save();
            } else {
                $meetingAgent = $this->meetingAgentFactory->create();
                $meetingAgent = $this->setMeetingData($meetingAgent, $params);
                $meetingAgent->setAdminId($user->getId());
                $meetingAgent->save();
            }
        }        
    }

    private function setMeetingData($meetingAgent, $params)
    {
        $meetingAgent->setPrimaryCategoryId(
            (isset($params['primary_category_id']) ? $params['primary_category_id'] : 0)
        );
        $meetingAgent->setSecondaryCategoryId(
            (isset($params['secondary_category_id']) ? $params['secondary_category_id'] : 0)
        );
        $meetingAgent->setZoomUserId(
            (isset($params['zoom_user_id']) ? $params['zoom_user_id'] : 0)
        );
        $meetingAgent->setIsAgent(
            (isset($params['is_agent']) ? $params['is_agent'] : 0)
        );

        return $meetingAgent;
    }
}

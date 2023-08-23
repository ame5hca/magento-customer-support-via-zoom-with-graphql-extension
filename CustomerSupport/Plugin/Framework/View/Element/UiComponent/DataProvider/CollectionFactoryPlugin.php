<?php

namespace AmeshExtensions\CustomerSupport\Plugin\Framework\View\Element\UiComponent\DataProvider;

use Magento\Framework\View\Element\UiComponent\DataProvider\CollectionFactory;
use AmeshExtensions\CustomerSupport\Model\ResourceModel\MeetingAgent\CollectionFactory as AgentCollectionFactory;
use Magento\Backend\Model\Auth\Session;

class CollectionFactoryPlugin
{
    private $adminSession;

    private $agentCollectionFactory;

    public function __construct(
        Session $adminSession,
        AgentCollectionFactory $agentCollectionFactory
    ) {
        $this->adminSession = $adminSession;
        $this->agentCollectionFactory = $agentCollectionFactory;
    }

    public function afterGetReport(
        CollectionFactory $subject,
        $collection,
        $requestName
    ) {
        $adminId = $this->adminSession->getUser()->getId();
        $isAgent = $this->isAgent($adminId);
        if ($requestName == 'meeting_grid_data_source') {
            if ($isAgent) {
                $collection->addFilterToMap('created_at', 'main_table.created_at');
                $now = new \DateTime();
                $collection->addFieldToFilter(
                    'created_at',
                    ['gteq' => $now->format('Y-m-d 00:00:00')]
                );
                $collection->addFieldToFilter(
                    'created_at',
                    ['lteq' => $now->format('Y-m-d 23:59:59')]
                );

                $collection->addFieldToFilter(
                    'status',
                    ['neq' => 'ended']
                );
                $collection->addFieldToFilter(
                    'agent_id',
                    ['eq' => $adminId]
                );
            } else {
                $collection->setOrder('main_table.created_at', 'DESC');
            }
            $collection->getSelect()->joinLeft(
                ['agent_info' => 'customersupport_agent'],
                'main_table.agent_id = agent_info.admin_id',
                []
            );
            $collection->getSelect()->joinLeft(
                ['users' => 'admin_user'],
                'main_table.agent_id = users.user_id',
                ['firstname']
            );
        }
        if ($requestName == 'participant_grid_data_source') {
            $collection->getSelect()->join(
                ['meeting' => 'customersupport_meeting'],
                'main_table.meeting_id = meeting.entity_id',
                []
            );
            if ($isAgent) {
                $collection->getSelect()->where(
                    'meeting.agent_id = ' . $adminId
                );
            }
            $collection->setOrder('created_at', 'DESC');
        }
        
        if ($requestName == 'callback_requests_grid_data_source') {            
            /**
             * Currently filtering the callback request by admin id is
             * disabled as per the requirement. 
             */
            /* $collection->getSelect()->where(
                'agent_id = '.$adminId
            );
            $collection->getSelect()->orWhere(
                'agent_id IS NULL '
            ); */
            $categoryIds = $this->getAgentSupportCategories(
                $adminId
            );
            if (is_array($categoryIds)) {
                $collection->getSelect()->where(
                    'category_id IN ('.implode(',', $categoryIds).')'
                );
            }            
            $collection->setOrder('created_at', 'DESC');
        }

        return $collection;
    }

    private function getAgentSupportCategories($agentId)
    {
        $agentCollection = $this->agentCollectionFactory->create();
        $agentCollection->addFieldToSelect(
            ['primary_category_id', 'secondary_category_id', 'is_agent']
        );
        $agentCollection->addFieldToFilter('admin_id', ['eq' => $agentId]);
        if ($agentCollection->getSize() > 0) {
            $agent = $agentCollection->getFirstItem();
            if (!$agent->getIsAgent()) {
                return false;
            }
            return [
                $agent->getPrimaryCategoryId(),
                $agent->getSecondaryCategoryId()
            ];
        }

        return false;
    }

    private function isAgent($adminId)
    {
        $agentCollection = $this->agentCollectionFactory->create();
        $agentCollection->addFieldToFilter('admin_id', ['eq' => $adminId]);
        if ($agentCollection->getSize() > 0) {
            $agent = $agentCollection->getFirstItem();
            if ($agent->getIsAgent()) {
                return true;
            }
        }
        return false;
    }
}

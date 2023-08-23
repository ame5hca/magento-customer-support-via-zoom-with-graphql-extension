<?php

namespace AmeshExtensions\CustomerSupport\Controller\Adminhtml\Sync;

use Magento\Backend\App\Action;
use AmeshExtensions\CustomerSupport\Logger\Zoom\Logger;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use AmeshExtensions\CustomerSupport\Model\Zoom\MeetingApi;
use AmeshExtensions\CustomerSupport\Model\ZoomHostUsersFactory;

class ZoomUsers extends Action
{
    protected $resultJsonFactory;

    private $zoomHostUsersFactory;

    private $meetingApi;

    private $logger;

    public function __construct(
        ZoomHostUsersFactory $zoomHostUsersFactory,
        JsonFactory $resultJsonFactory,
        MeetingApi $meetingApi,
        Context $context,        
        Logger $logger
    ) {
        $this->zoomHostUsersFactory = $zoomHostUsersFactory;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->meetingApi = $meetingApi;
        $this->logger = $logger;
        parent::__construct($context);
    }

    /**
     * Update zoom user data
     */
    public function execute()
    {
        $returnData = ['status' => false];
        try {
            $result = $this->resultJsonFactory->create(); 
            $usersData = $this->meetingApi->listUsers();
            if (count($usersData['users']) > 0) {
                $this->truncateTable();
                foreach ($usersData['users'] as $user) {
                    $zoomUserModel = $this->zoomHostUsersFactory->create();
                    $zoomUserModel->setUserId($user->id);
                    $zoomUserModel->setFirstName($user->first_name);
                    $zoomUserModel->setLastName($user->last_name);
                    $zoomUserModel->setEmail($user->email);
                    $zoomUserModel->setType($user->type);
                    $zoomUserModel->setStatus($user->status);
                    $zoomUserModel->save();
                }
            }
            $returnData['status'] = true;
        } catch (\Exception $e) {

        }    
        
        return $result->setData($returnData);
    }

    private function truncateTable()
    {
        $zoomUserModel = $this->zoomHostUsersFactory->create();
        $connection = $zoomUserModel->getResource()->getConnection();
        $tableName = $zoomUserModel->getResource()->getMainTable();
        $connection->truncateTable($tableName);
    }
}

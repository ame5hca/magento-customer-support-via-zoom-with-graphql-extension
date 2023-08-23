<?php

namespace AmeshExtensions\CustomerSupport\Controller\Adminhtml\Meeting;

use Magento\Framework\Exception\LocalizedException;
use Magento\Backend\App\Action;
use AmeshExtensions\CustomerSupport\Logger\Zoom\Logger;
use Magento\Backend\App\Action\Context;
use AmeshExtensions\CustomerSupport\Model\Zoom\ServiceProvider\MeetingApiConfigManager;
use Magento\Backend\Model\Auth\Session as AdminSession;
use AmeshExtensions\CustomerSupport\Model\Zoom\MeetingApi;
use AmeshExtensions\CustomerSupport\Model\MeetingManager;
use AmeshExtensions\CustomerSupport\Model\ResourceModel\MeetingAgent\CollectionFactory as MeetingAgentCollectionFactory;

class Save extends Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'AmeshExtensions_CustomerSupport::create_meeting';

    protected $meetingApiConfigManager;

    protected $meetingAgentCollectionFactory;

    protected $meetingApi;

    protected $meetingManager;

    protected $adminSession;

    private $logger;

    public function __construct(
        Context $context,
        AdminSession $adminSession,
        MeetingManager $meetingManager,
        MeetingApiConfigManager $meetingApiConfigManager,
        MeetingAgentCollectionFactory $meetingAgentCollectionFactory,
        MeetingApi $meetingApi,
        Logger $logger
    ) {
        $this->meetingAgentCollectionFactory = $meetingAgentCollectionFactory;
        $this->meetingApiConfigManager = $meetingApiConfigManager;
        $this->meetingApi = $meetingApi;
        $this->meetingManager = $meetingManager;
        $this->adminSession = $adminSession;
        $this->logger = $logger;
        parent::__construct($context);
    }

    /**
     * Save action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $data = $this->getRequest()->getPostValue();
        if (!$data) {
            $this->messageManager->addErrorMessage(
                __('Data is missing')
            );
            return $resultRedirect->setPath('*/*/');
        }
        $adminUser = $this->adminSession->getUser();
        $zoomUserId = $this->getZoomUserId(
            $adminUser->getId()
        );
        if ($zoomUserId == null) {
            throw new LocalizedException(
                __('No zoom account email is added to your account. Please check.')
            );
            
        }
        /**
         * '-' is used as a reserved character for appending
         * meeting and user id in subject while meeting
         * update operation. So - will be replaced with
         * ':' in order to avoid the conflict.
         */
        $data['topic'] = str_replace('-', ':', $data['topic']);
        $data['duration'] = (int) $data['duration'];
        try {            
            $this->meetingApiConfigManager->create($data);
            $this->meetingApiConfigManager->setAgentId($adminUser->getId());
            $this->meetingApiConfigManager->setAgentEmail($zoomUserId);
            $this->meetingApiConfigManager->setType(2);
            $this->meetingApiConfigManager->setSettings(
                [
                    'host_video' => true,
                    'participant_video' => true,
                    'approval_type' => 2
                ]
            );

            $meetingInfo = $this->meetingApi->createNewMeeting();
            $mid = $this->meetingManager->saveMeetingInfo($meetingInfo);

            return $resultRedirect->setPath('*/*/edit', ['id' => $mid]);
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\Exception $e) {
            $this->logger->critical('SaveNewMeetingError: ' . $e->getMessage());
            $this->messageManager->addErrorMessage(
                __('Something went wrong while creating the meeting.')
            );
        }
        return $resultRedirect->setPath('*/*/');
    }

    private function getZoomUserId($adminUserId)
    {
        $agentCollection = $this->meetingAgentCollectionFactory->create();
        $agentCollection->addFieldToFilter('admin_id', ['eq' => $adminUserId]);
        if ($agentCollection->getSize() > 0) {
            return $agentCollection->getFirstItem()->getZoomUserId();
        }
        return null;
    }
}

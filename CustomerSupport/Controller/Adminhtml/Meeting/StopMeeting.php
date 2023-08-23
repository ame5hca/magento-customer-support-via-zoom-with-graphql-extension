<?php

namespace AmeshExtensions\CustomerSupport\Controller\Adminhtml\Meeting;

use Magento\Framework\Exception\LocalizedException;
use Magento\Backend\App\Action;
use AmeshExtensions\CustomerSupport\Logger\Zoom\Logger;
use Magento\Backend\App\Action\Context;
use AmeshExtensions\CustomerSupport\Model\Zoom\MeetingApi;
use AmeshExtensions\CustomerSupport\Model\MeetingManager;
use AmeshExtensions\CustomerSupport\Model\MeetingListFactory;

class StopMeeting extends Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'AmeshExtensions_CustomerSupport::create_meeting';

    protected $meetingApi;

    protected $meetingManager;

    protected $meetingListFactory;

    private $logger;

    public function __construct(
        Context $context,
        MeetingManager $meetingManager,
        MeetingApi $meetingApi,
        MeetingListFactory $meetingListFactory,
        Logger $logger
    ) {
        $this->meetingApi = $meetingApi;
        $this->meetingManager = $meetingManager;
        $this->meetingListFactory = $meetingListFactory;
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
        $meetingId = $this->getRequest()->getParam('id', null);
        if (empty($meetingId)) {
            $this->messageManager->addErrorMessage(
                __('Meeting id is missing')
            );
            return $resultRedirect->setPath('*/*/');
        }

        try {
            $meetingModel = $this->meetingListFactory->create();
            $meeting = $meetingModel->load($meetingId);
            if (!$meeting->getId()) {
                $this->messageManager->addErrorMessage(
                    __('Meeting is missing')
                );
                return $resultRedirect->setPath('*/*/');
            }
            $this->meetingApi->endMeeting($meeting->getMeetingId());
            $this->meetingManager->stopMeeting($meeting);

            $this->messageManager->addSuccessMessage(
                __('Meeting stopped.')
            );
            return $resultRedirect->setPath('*/*/');
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\Exception $e) {
            $this->logger->critical('StopMeetingError: ' . $e->getMessage());
            $this->messageManager->addErrorMessage(
                __('Something went wrong while ending the meeting.')
            );
        }
        return $resultRedirect->setPath('*/*/');
    }
}

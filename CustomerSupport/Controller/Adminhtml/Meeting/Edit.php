<?php

namespace AmeshExtensions\CustomerSupport\Controller\Adminhtml\Meeting;

use AmeshExtensions\CustomerSupport\Model\MeetingListFactory;
use Magento\Backend\App\Action\Context;
use Magento\Backend\App\Action;
use AmeshExtensions\CustomerSupport\Logger\Zoom\Logger;
use Magento\Framework\Exception\LocalizedException;
use AmeshExtensions\CustomerSupport\Model\ResourceModel\MeetingList as MeetingResourceModel;
use Magento\Framework\View\Result\PageFactory;
use AmeshExtensions\CustomerSupport\Model\ServiceProvider\Registry\MeetingRegistry;

class Edit extends Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'AmeshExtensions_CustomerSupport::create_meeting';

    private $meetingFactory;

    private $logger;

    private $meetingRegistry;

    private $resultPageFactory;

    public function __construct(
        Context $context,
        MeetingListFactory $meetingFactory,
        MeetingRegistry $meetingRegistry,
        PageFactory $resultPageFactory,
        Logger $logger
    ) {
        $this->meetingFactory = $meetingFactory;
        $this->meetingRegistry = $meetingRegistry;
        $this->resultPageFactory = $resultPageFactory;
        $this->logger = $logger;
        parent::__construct($context);
    }

    /**
     * Edit action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $mid = $this->getRequest()->getParam('id');
        try {
            if (empty($mid)) {
                throw new LocalizedException(
                    __('Meeting id is missing.')
                );
            }
            $meetingModel = $this->meetingFactory->create();
            $meeting = $meetingModel->load($mid);
            if (empty($meeting)) {
                throw new LocalizedException(
                    __('No such meeting with this id exist.')
                );
            }
            $this->meetingRegistry->register(
                MeetingResourceModel::MEETING_REGISTRY_ID,
                $meeting
            );

            $resultPage = $this->resultPageFactory->create();
            $this->initPage($resultPage)->addBreadcrumb(
                __('Meeting Info'),
                __('Meeting Info')
            );
            $resultPage->getConfig()->getTitle()->prepend(
                __('Meeting Info #' . $meeting->getMeetingId())
            );
            return $resultPage;
        } catch (LocalizedException $le) {
            return $this->redirectWithError(
                $le->getMessage(),
                $le,
                false
            );
        } catch (\Exception $e) {
            return $this->redirectWithError(
                'Something went wrong while fetching the meeting info.',
                $e
            );
        }
    }

    private function initPage($resultPage)
    {
        $resultPage->setActiveMenu('AmeshExtensions_CustomerSupport::top')
            ->addBreadcrumb(__('AmeshExtensions'), __('AmeshExtensions'))
            ->addBreadcrumb(__('Customer Support'), __('Customer Support'));
        return $resultPage;
    }

    private function redirectWithError($customErrorMessage, $exception, $logError = true)
    {
        $this->messageManager->addErrorMessage(
            __($customErrorMessage)
        );
        if ($logError) {
            $this->logger->critical(
                'EditMeetingInfoError:' . $exception->getMessage()
            );
        }
        $resultRedirect = $this->resultRedirectFactory->create();
        return $resultRedirect->setPath('*/*/');
    }
}

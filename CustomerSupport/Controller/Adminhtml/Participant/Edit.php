<?php

namespace AmeshExtensions\CustomerSupport\Controller\Adminhtml\Participant;

use AmeshExtensions\CustomerSupport\Model\MeetingParticipantFactory;
use Magento\Backend\App\Action\Context;
use Magento\Backend\App\Action;
use AmeshExtensions\CustomerSupport\Logger\Zoom\Logger;
use Magento\Framework\Exception\LocalizedException;
use AmeshExtensions\CustomerSupport\Model\ResourceModel\MeetingParticipant as ParticipantResourceModel;
use Magento\Framework\View\Result\PageFactory;
use AmeshExtensions\CustomerSupport\Model\ServiceProvider\Registry\ParticipantRegistry;

class Edit extends Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'AmeshExtensions_CustomerSupport::edit_participants_info';

    private $meetingParticipantFactory;

    private $logger;

    private $participantRegistry;

    private $resultPageFactory;

    public function __construct(
        Context $context,
        MeetingParticipantFactory $meetingParticipantFactory,
        ParticipantRegistry $participantRegistry,
        PageFactory $resultPageFactory,
        Logger $logger
    ) {
        $this->meetingParticipantFactory = $meetingParticipantFactory;
        $this->participantRegistry = $participantRegistry;
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
                    __('Meeting participant id is missing.')
                );
            }
            $participantModel = $this->meetingParticipantFactory->create();
            $meetingparticipant = $participantModel->load($mid);
            if (empty($meetingparticipant)) {
                throw new LocalizedException(
                    __('No such meeting participant joined with this id.')
                );
            }
            $this->participantRegistry->register(
                ParticipantResourceModel::MEETING_PARTICIPANT_REGISTRY_ID,
                $meetingparticipant
            );

            $resultPage = $this->resultPageFactory->create();
            $this->initPage($resultPage)->addBreadcrumb(
                __('Meeting Participant Info'),
                __('Meeting Participant Info')
            );
            $resultPage->getConfig()->getTitle()->prepend(
                __('Meeting Participant Info')
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
                'Something went wrong while fetching the meeting participant info.',
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
                'EditMeetingparticipantInfoError:' . $exception->getMessage()
            );
        }
        $resultRedirect = $this->resultRedirectFactory->create();
        return $resultRedirect->setPath('*/*/');
    }
}

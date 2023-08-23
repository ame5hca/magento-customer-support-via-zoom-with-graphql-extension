<?php

namespace AmeshExtensions\CustomerSupport\Controller\Adminhtml\Participant;

use Magento\Framework\Exception\LocalizedException;
use Magento\Backend\App\Action;
use AmeshExtensions\CustomerSupport\Logger\Zoom\Logger;
use Magento\Backend\App\Action\Context;
use AmeshExtensions\CustomerSupport\Model\MeetingParticipantFactory;
use Magento\Framework\App\Request\DataPersistorInterface;
use AmeshExtensions\CustomerSupport\Model\ResourceModel\MeetingParticipant as ParticipantResourceModel;

class Save extends Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'AmeshExtensions_CustomerSupport::edit_participants_info';
    
    private $meetingParticipantFactory;

    private $dataPersistor;

    private $logger;

    public function __construct(
        Context $context,
        DataPersistorInterface $dataPersistor,
        MeetingParticipantFactory $meetingParticipantFactory,
        Logger $logger
    ) {
        $this->meetingParticipantFactory = $meetingParticipantFactory;
        $this->dataPersistor = $dataPersistor;
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
        $data = $this->getRequest()->getPostValue('meeting_feedback');
        if (!$data || empty($data['entity_id'])) {
            $this->messageManager->addErrorMessage(
                __('Data is missing')
            );
            return $resultRedirect->setPath('*/*/');
        }
        try {
            $meetingParticipantModel = $this->meetingParticipantFactory->create();
            $meetingParticipant = $meetingParticipantModel->load($data['entity_id']);
            $meetingParticipant->setBuyPercentage($data['buy_percentage']);
            $meetingParticipant->setRemarks($data['remarks']);
            $meetingParticipant->save();


            $this->messageManager->addSuccessMessage(
                __('Successfully updated the info.')
            );
            $this->dataPersistor->clear(
                ParticipantResourceModel::MEETING_PARTICIPANT_REGISTRY_ID
            );

            return $resultRedirect->setPath('*/*/');
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\Exception $e) {
            $this->logger->critical('UpdateMeetingParticipantInfoError: ' . $e->getMessage());
            $this->messageManager->addErrorMessage(
                __('Something went wrong while updating the data.')
            );
        }
        $this->dataPersistor->set(
            ParticipantResourceModel::MEETING_PARTICIPANT_REGISTRY_ID,
            $data
        );

        return $resultRedirect->setPath('*/*/');
    }
}

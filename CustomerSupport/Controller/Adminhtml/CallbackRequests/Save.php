<?php

namespace AmeshExtensions\CustomerSupport\Controller\Adminhtml\CallbackRequests;

use Magento\Framework\Exception\LocalizedException;
use Magento\Backend\App\Action;
use AmeshExtensions\CustomerSupport\Logger\Zoom\Logger;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Request\DataPersistorInterface;
use AmeshExtensions\CustomerSupport\Model\MeetingCallbackRequestsFactory;
use AmeshExtensions\CustomerSupport\Model\ResourceModel\MeetingCallbackRequests as CallbackRequestResource;
use Magento\Backend\Model\Auth\Session as AdminSession;

class Save extends Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'AmeshExtensions_CustomerSupport::update_callback_request';
    
    private $callbackRequestFactory;

    private $dataPersistor;

    private $logger;

    private $adminSession;

    public function __construct(
        Context $context,
        DataPersistorInterface $dataPersistor,
        MeetingCallbackRequestsFactory $callbackRequestFactory,
        AdminSession $adminSession,
        Logger $logger
    ) {
        $this->callbackRequestFactory = $callbackRequestFactory;
        $this->dataPersistor = $dataPersistor;
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
        $data = $this->getRequest()->getPostValue('callback_form');
        if (!$data || empty($data['entity_id'])) {
            $this->messageManager->addErrorMessage(
                __('Data is missing')
            );
            return $resultRedirect->setPath('*/*/');
        }
        try {
            $adminUser = $this->adminSession->getUser();
            $callbackRequestModel = $this->callbackRequestFactory->create();
            $callbackRequest = $callbackRequestModel->load($data['entity_id']);
            $callbackRequest->setStatus($data['status']);
            if ($data['status'] == 'contacting') {
                $callbackRequest->setAgentId($adminUser->getId());
                $callbackRequest->setAgentName($adminUser->getName());
            }
            $callbackRequest->save();


            $this->messageManager->addSuccessMessage(
                __('Successfully updated the status.')
            );
            $this->dataPersistor->clear(
                CallbackRequestResource::CALLBACK_REQUEST_REGISTRY_ID
            );

            return $resultRedirect->setPath('*/*/');
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\Exception $e) {
            $this->logger->critical('UpdateCallbackRequestStatusError: ' . $e->getMessage());
            $this->messageManager->addErrorMessage(
                __('Something went wrong while updating the data.')
            );
        }
        $this->dataPersistor->set(
            CallbackRequestResource::CALLBACK_REQUEST_REGISTRY_ID,
            $data
        );

        return $resultRedirect->setPath('*/*/');
    }
}

<?php

namespace AmeshExtensions\CustomerSupport\Controller\Adminhtml\CallbackRequests;

use Magento\Backend\App\Action\Context;
use Magento\Backend\App\Action;
use AmeshExtensions\CustomerSupport\Logger\Zoom\Logger;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Result\PageFactory;
use AmeshExtensions\CustomerSupport\Model\MeetingCallbackRequestsFactory;
use AmeshExtensions\CustomerSupport\Model\ResourceModel\MeetingCallbackRequests as MeetingCallbackRequestsResource;
use AmeshExtensions\CustomerSupport\Model\ServiceProvider\Registry\CallbackRequestRegistry;

class Edit extends Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'AmeshExtensions_CustomerSupport::update_callback_request';

    private $meetingCallbackRequestsFactory;

    private $callbackRequestRegistry;

    private $logger;

    private $resultPageFactory;

    public function __construct(
        Context $context,
        MeetingCallbackRequestsFactory $meetingCallbackRequestsFactory,
        CallbackRequestRegistry $callbackRequestRegistry,
        PageFactory $resultPageFactory,
        Logger $logger
    ) {
        $this->meetingCallbackRequestsFactory = $meetingCallbackRequestsFactory;
        $this->callbackRequestRegistry = $callbackRequestRegistry;
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
        $requestId = $this->getRequest()->getParam('id');
        try {
            if (empty($requestId)) {
                throw new LocalizedException(
                    __('Callback request id is missing.')
                );
            }
            $callbackRequestModel = $this->meetingCallbackRequestsFactory->create();
            $callbackRequest = $callbackRequestModel->load($requestId);
            if (empty($callbackRequest)) {
                throw new LocalizedException(
                    __('No such request is recieved with this id.')
                );
            }
            $this->callbackRequestRegistry->register(
                MeetingCallbackRequestsResource::CALLBACK_REQUEST_REGISTRY_ID,
                $callbackRequest
            );

            $resultPage = $this->resultPageFactory->create();
            $this->initPage($resultPage)->addBreadcrumb(
                __('Callback Requests'),
                __('Callback Requests')
            );
            $resultPage->getConfig()->getTitle()->prepend(
                __('Callback Requests')
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
                'Something went wrong while fetching the callback requests.',
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
                'EditCallbackRequestsError:' . $exception->getMessage()
            );
        }
        $resultRedirect = $this->resultRedirectFactory->create();
        return $resultRedirect->setPath('*/*/');
    }
}

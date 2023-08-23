<?php

namespace AmeshExtensions\CustomerSupport\Model;

use AmeshExtensions\CustomerSupport\Logger\Zoom\Logger;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use AmeshExtensions\CustomerSupport\Model\ResourceModel\MeetingCallbackRequests\CollectionFactory;

class CallbackRequestManager
{
    private $callbackRequestCollectionFactory;

    private $callbackRequestFactory;

    private $logger;

    private $datetime;

    public function __construct(
        MeetingCallbackRequestsFactory $callbackRequestFactory,
        CollectionFactory $callbackRequestCollectionFactory,
        TimezoneInterface $datetime,
        Logger $logger
    ) {
        $this->callbackRequestCollectionFactory = $callbackRequestCollectionFactory;
        $this->callbackRequestFactory = $callbackRequestFactory;
        $this->datetime = $datetime;
        $this->logger = $logger;
    }
    
    public function saveRequest($data)
    {
        $callbackRequest = $this->callbackRequestFactory->create();
        $callbackRequest->setData($data);
        $callbackRequest->save();

        return true;
    }

    public function checkExistingCallbackRequest($customerId, $categoryId)
    {
        $collection = $this->callbackRequestCollectionFactory->create();
        $collection->addFieldToFilter('customer_id', ['eq' => $customerId]);
        $collection->addFieldToFilter('category_id', ['eq' => $categoryId]);
        $collection->addFieldToFilter(
            'status', ['in' => ['requested','contacting']]
        );
        if ($collection->getSize() > 0) {
            throw new LocalizedException(
                __("You have already requested for a support callback. Sorry for the delay since all the agents are busy. We will contact you shortly.")
            );            
        }
       
    }
}

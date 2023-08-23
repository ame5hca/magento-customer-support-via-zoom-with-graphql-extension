<?php

namespace AmeshExtensions\CustomerSupportGraphQl\Model\Zoom;

use Magento\Framework\Exception\LocalizedException;
use AmeshExtensions\PincodeChecker\Model\ResourceModel\Pincode\CollectionFactory;

class ServiceLocationValidator
{
    private $deliveryCollectionFactory;

    public function __construct(
        CollectionFactory $deliveryCollectionFactory
    ) {
        $this->deliveryCollectionFactory = $deliveryCollectionFactory;
    }

    public function isServiceAvailable($districtCode)
    {
        $deliveryListCollection = $this->deliveryCollectionFactory->create();
        $deliveryListCollection->addFieldToFilter(
            'district',
            ['like' => '%' . $districtCode . '%']
        );
        if ($deliveryListCollection->getSize() <= 0) {
            throw new LocalizedException(
                __('No service is available in your location.')
            );
        }
    }
}

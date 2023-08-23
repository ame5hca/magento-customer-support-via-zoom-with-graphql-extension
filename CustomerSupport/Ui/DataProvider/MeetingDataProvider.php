<?php

namespace AmeshExtensions\CustomerSupport\Ui\DataProvider;

use Magento\Ui\DataProvider\AbstractDataProvider;
use AmeshExtensions\CustomerSupport\Model\ResourceModel\MeetingList\Collection;

class MeetingDataProvider extends AbstractDataProvider
{
    protected $collection;

    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        Collection $collectionFactory,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $collectionFactory;
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    /**
     * Get data
     *
     * @return array
     */
    public function getData()
    {
        return [];
    }
}

<?php

namespace AmeshExtensions\CustomerSupport\Ui\DataProvider;

use Magento\Ui\DataProvider\AbstractDataProvider;
use AmeshExtensions\CustomerSupport\Model\ResourceModel\MeetingCallbackRequests\CollectionFactory;
use Magento\Framework\App\Request\DataPersistorInterface;
use AmeshExtensions\CustomerSupport\Model\ResourceModel\MeetingCallbackRequests as CallbackRequestResource;

class CallbackRequestDataProvider extends AbstractDataProvider
{
    protected $collection;

    protected $dataPersistor;

    protected $loadedData;

    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        DataPersistorInterface $dataPersistor,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->collection = $collectionFactory->create();
        $this->dataPersistor = $dataPersistor;        
    }

    /**
     * Get data
     *
     * @return array
     */
    public function getData()
    {
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }
        $items = $this->collection->getItems();
        foreach ($items as $model) {            
            $this->loadedData[$model->getId()] = [
                'callback_form' => [
                    'entity_id' => $model->getData('entity_id'),
                    'status' => $model->getData('status')
                ]
            ];
        }
        $data = $this->dataPersistor->get(
            CallbackRequestResource::CALLBACK_REQUEST_REGISTRY_ID
        );
        
        if (!empty($data)) {
            $model = $this->collection->getNewEmptyItem();
            $model->setData($data);
            $this->loadedData[$model->getId()] = [
                'callback_form' => $model->getData()
            ];
            $this->dataPersistor->clear(
                CallbackRequestResource::CALLBACK_REQUEST_REGISTRY_ID
            );
        }
        
        return $this->loadedData;
    }
}

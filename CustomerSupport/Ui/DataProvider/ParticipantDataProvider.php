<?php

namespace AmeshExtensions\CustomerSupport\Ui\DataProvider;

use Magento\Ui\DataProvider\AbstractDataProvider;
use AmeshExtensions\CustomerSupport\Model\ResourceModel\MeetingParticipant\CollectionFactory;
use Magento\Framework\App\Request\DataPersistorInterface;
use AmeshExtensions\CustomerSupport\Model\ResourceModel\MeetingParticipant as ParticipantResourceModel;

class ParticipantDataProvider extends AbstractDataProvider
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
                'meeting_feedback' => [
                    'entity_id' => $model->getData('entity_id'),
                    'buy_percentage' => $model->getData('buy_percentage'),
                    'remarks' => $model->getData('remarks')
                ]
            ];
        }
        $data = $this->dataPersistor->get(
            ParticipantResourceModel::MEETING_PARTICIPANT_REGISTRY_ID
        );
        
        if (!empty($data)) {
            $model = $this->collection->getNewEmptyItem();
            $model->setData($data);
            $this->loadedData[$model->getId()] = [
                'meeting_feedback' => $model->getData()
            ];
            $this->dataPersistor->clear(
                ParticipantResourceModel::MEETING_PARTICIPANT_REGISTRY_ID
            );
        }
        
        return $this->loadedData;
    }
}

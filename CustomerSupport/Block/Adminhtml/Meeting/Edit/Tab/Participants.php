<?php

namespace AmeshExtensions\CustomerSupport\Block\Adminhtml\Meeting\Edit\Tab;

use Magento\Backend\Block\Widget\Grid\Extended;
use AmeshExtensions\CustomerSupport\Block\Adminhtml\Meeting\Edit\Tab\Renderer\Action;
use AmeshExtensions\CustomerSupport\Model\ResourceModel\MeetingParticipant\CollectionFactory;

class Participants extends Extended
{
    /**
     * @var  CollectionFactory
     */
    protected $collectionFactory;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        CollectionFactory $collectionFactory,
        array $data = []
    ) {
        $this->collectionFactory = $collectionFactory;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('customersupport_meeting_participants_grid');
        $this->setDefaultSort('created_at');
        $this->setDefaultDir('desc');
        $this->setUseAjax(true);
    }

    /**
     * Apply various selection filters to prepare the sales order grid collection.
     *
     * @return $this
     */
    protected function _prepareCollection()
    {
        $collection = $this->collectionFactory->create();
        $collection->addFieldToSelect(
            'entity_id'       
        )->addFieldToSelect(
            'participant_name'
        )->addFieldToSelect(
            'customer_id'
        )->addFieldToSelect(
            'buy_percentage'
        )->addFieldToSelect(
            'duration'
        )->addFieldToSelect(
            'join_time'
        )->addFieldToSelect(
            'left_time'
        )->addFieldToSelect(
            'status'
        )->addFieldToFilter(
            'meeting_id', ['eq' => $this->getRequest()->getParam('id')]
        );

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * @inheritdoc
     */
    protected function _prepareColumns()
    {
        $this->addColumn('entity_id', ['header' => __('ID'), 'width' => '100', 'index' => 'entity_id']);
        $this->addColumn('participant_name', ['header' => __('Name'), 'width' => '100', 'index' => 'participant_name']);
        $this->addColumn('customer_id', ['header' => __('Customer Id'), 'width' => '100', 'index' => 'customer_id']);
        $this->addColumn('buy_percentage', ['header' => __('Buy Percentage'), 'width' => '100', 'index' => 'buy_percentage']);
        $this->addColumn('duration', ['header' => __('Duration(in minutes)'), 'width' => '100', 'index' => 'duration']);
        $this->addColumn('join_time', ['header' => __('Join Time'), 'width' => '100', 'index' => 'join_time']);
        $this->addColumn('left_time', ['header' => __('Left Time'), 'width' => '100', 'index' => 'left_time']);
        $this->addColumn('status', ['header' => __('Status'), 'width' => '100', 'index' => 'status']);

        $this->addColumn(
            'action',
            [
                'header' => 'Action',
                'filter' => false,
                'sortable' => false,
                'width' => '100px',
                'renderer' => Action::class
            ]
        );
        

        return parent::_prepareColumns();
    }


    /**
     * @inheritdoc
     */
    public function getGridUrl()
    {
        return $this->getUrl('customersupport/*/allproductsgrid', ['_current' => true]);
    }
}

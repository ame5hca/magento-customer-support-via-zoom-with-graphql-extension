<?php

namespace AmeshExtensions\CustomerSupport\Block\Adminhtml\Participant\Edit\Tab;

use Magento\Backend\Block\Widget\Grid\Extended;
use AmeshExtensions\CustomerSupport\Block\Adminhtml\Participant\Edit\Tab\Renderer\Action;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Catalog\Model\Product\Attribute\Source\Status;

class AllProducts extends Extended
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
        $this->setId('customersupport_participant_products_grid');
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
            'name'
        )->addFieldToSelect(
            'sku'
        )->addFieldToSelect(
            'visibility'
        )->addFieldToSelect(
            'status'
        )->addFieldToSelect(
            'price'
        )->addFieldToFilter('status', ['eq' => Status::STATUS_ENABLED]);

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * @inheritdoc
     */
    protected function _prepareColumns()
    {
        $this->addColumn('entity_id', ['header' => __('ID'), 'width' => '100', 'index' => 'entity_id']);
        $this->addColumn('name', ['header' => __('Name'), 'width' => '100', 'index' => 'name']);
        $this->addColumn('sku', ['header' => __('Sku'), 'width' => '100', 'index' => 'sku']);
        $this->addColumn('visibility', ['header' => __('Visibility'), 'width' => '100', 'index' => 'visibility']);
        $this->addColumn('status', ['header' => __('Status'), 'width' => '100', 'index' => 'status']);
        $this->addColumn('price', ['header' => __('Price'), 'width' => '100', 'index' => 'price']);

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

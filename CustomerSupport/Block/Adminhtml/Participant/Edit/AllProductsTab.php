<?php

namespace AmeshExtensions\CustomerSupport\Block\Adminhtml\Participant\Edit;

use Magento\Ui\Component\Layout\Tabs\TabWrapper;

class AllProductsTab extends TabWrapper
{
    /**
     * @var bool
     */
    protected $isAjaxLoaded = true;
    
    /**
     * @inheritdoc
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Return Tab label
     *
     * @codeCoverageIgnore
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabLabel()
    {
        return __('Products For Add To Cart');
    }

    /**
     * Return URL link to Tab content
     *
     * @return string
     */
    public function getTabUrl()
    {
        return $this->getUrl('customersupport/participant/allproducts', ['_current' => true]);
    }
}

<?php

namespace AmeshExtensions\CustomerSupport\Block\System\Config;

use Magento\Config\Block\System\Config\Form\Field;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Data\Form\Element\AbstractElement;

class SyncZoomUserButton extends Field
{
    protected $_template = 'AmeshExtensions_CustomerSupport::system/config/sync_zoom_user_button.phtml';

    public function __construct(
        Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    public function render(AbstractElement $element)
    {
        $element->unsScope()->unsCanUseWebsiteValue()->unsCanUseDefaultValue();
        return parent::render($element);
    }

    protected function _getElementHtml(AbstractElement $element)
    {
        return $this->_toHtml();
    }

    public function getActionUrl()
    {
        return $this->getUrl('customersupport/sync/zoomusers');
    }

    public function getButtonHtml()
    {
        $button = $this->getLayout()->createBlock(
            'Magento\Backend\Block\Widget\Button'
        )->setData(
            [
                'id' => 'sync_zoom_account',
                'class' => 'action primary',
                'label' => __('Sync Zoom Accounts'),
            ]
        );
        return $button->toHtml();
    }
}

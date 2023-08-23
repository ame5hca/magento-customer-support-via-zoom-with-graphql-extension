<?php

namespace AmeshExtensions\CustomerSupport\Block\Adminhtml\Meeting\Edit\Tab\Renderer;

use Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer;

class Action extends AbstractRenderer
{
    /**
     * Array to store all options data
     *
     * @var array
     */
    protected $_actions = [];

    /**
     * Render actions
     *
     * @param \Magento\Framework\DataObject $row
     * @return string
     */
    public function render(\Magento\Framework\DataObject $row)
    {
        if ($row->getStatus() == 'waiting') {
            return '';
        }
        $this->_actions = [];
        $editAction = [
            '@' => [
                'href' => $this->getUrl('customersupport/participant/edit', ['id' => $row->getId()]),
            ],
            '#' => __('Edit'),
        ];
        $this->addToActions($editAction);
        return $this->_actionsToHtml();
    }

    /**
     * Get escaped value
     *
     * @param string $value
     * @return string
     */
    protected function _getEscapedValue($value)
    {
        // phpcs:ignore Magento2.Functions.DiscouragedFunction
        return addcslashes($this->escapeHtml($value), '\\\'');
    }

    /**
     * Render options array as a HTML string
     *
     * @param array $actions
     * @return string
     */
    protected function _actionsToHtml(array $actions = [])
    {
        $html = [];
        $attributesObject = new \Magento\Framework\DataObject();

        if (empty($actions)) {
            $actions = $this->_actions;
        }

        foreach ($actions as $action) {
            $attributesObject->setData($action['@']);
            $html[] = '<a ' . $attributesObject->serialize() . '>' . $action['#'] . '</a>';
        }
        return implode('', $html);
    }

    /**
     * Add one action array to all options data storage
     *
     * @param array $actionArray
     * @return void
     */
    public function addToActions($actionArray)
    {
        $this->_actions[] = $actionArray;
    }
}

<?php

namespace AmeshExtensions\CustomerSupport\Ui\Component\Listing\Column\CallbackRequests;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class status
 */
class Status implements OptionSourceInterface
{
    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        $status = [
            ['label' => 'Requested', 'value' => 'requested'],
            ['label' => 'Contacting', 'value' => 'contacting'],
            ['label' => 'Contacted', 'value' => 'contacted']
        ];

        array_walk(
            $status,
            function (&$item) {
                $item['__disableTmpl'] = true;
            }
        );

        return $status;
    }
}

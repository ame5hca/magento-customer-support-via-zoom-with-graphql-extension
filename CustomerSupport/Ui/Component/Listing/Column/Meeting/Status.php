<?php

namespace AmeshExtensions\CustomerSupport\Ui\Component\Listing\Column\Meeting;

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
            ['label' => 'Waiting', 'value' => 'waiting'],
            ['label' => 'Started', 'value' => 'started'],
            ['label' => 'In progress', 'value' => 'inprogress'],            
            ['label' => 'Ended', 'value' => 'ended']
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

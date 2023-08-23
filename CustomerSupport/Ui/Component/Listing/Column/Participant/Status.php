<?php

namespace AmeshExtensions\CustomerSupport\Ui\Component\Listing\Column\Participant;

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
            ['label' => 'Joined', 'value' => 'joined'],
            ['label' => 'Not Joined', 'value' => 'notjoined'],
            ['label' => 'Left', 'value' => 'left']
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

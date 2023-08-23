<?php

namespace AmeshExtensions\CustomerSupportGraphQl\Plugin\Resolver\DataModifier;

use AmeshExtensions\ExtendedSalesGraphQl\Model\Resolver\DataModifier\PlaceOrder;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use AmeshExtensions\CustomerSupport\Model\MeetingParticipantManager;

class PlaceOrderPlugin
{
    protected $meetingparticipantManager;

    public function __construct(
        MeetingParticipantManager $meetingparticipantManager
    ) {
        $this->meetingparticipantManager = $meetingparticipantManager;
    }

    public function afterModify(
        PlaceOrder $subject,
        $result,
        $subjectResult,
        $order,
        $args
    ) {
        try {
            if (!isset($result['order'])) {
                throw new GraphQlInputException(
                    __('Order data is missing')
                );
            }
            $this->meetingparticipantManager->linkOrderToMeeting(
                $order->getIncrementId(),
                $order->getCustomerId()
            );
            
        } catch (\Exception $le) {
            throw new GraphQlInputException(
                __('Order linking to aggent failed: %message', ['message' => $le->getMessage()]),
                $le
            );
        }
        return $result;
    }
}

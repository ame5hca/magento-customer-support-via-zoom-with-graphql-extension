<?php

declare(strict_types=1);

namespace AmeshExtensions\CustomerSupportGraphQl\Model\Resolver;

use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\Resolver\ContextInterface;
use Magento\Framework\GraphQl\Query\Resolver\Value;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\GraphQl\Exception\GraphQlAuthorizationException;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use AmeshExtensions\CustomerSupportGraphQl\Model\Zoom\GetZoomMeeting;
use AmeshExtensions\CustomerSupportGraphQl\Model\Zoom\ServiceLocationValidator;

/**
 * Class VideoSupport
 *
 * @package AmeshExtensions\CustomerSupportGraphQl\Model\Resolver
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class VideoSupport implements ResolverInterface
{
    private $zoomMeetingProvider;

    private $serviceLocationValidator;

    public function __construct(
        GetZoomMeeting $zoomMeetingProvider,
        ServiceLocationValidator $serviceLocationValidator
    ) {
        $this->zoomMeetingProvider = $zoomMeetingProvider;
        $this->serviceLocationValidator = $serviceLocationValidator;
    }

    /**
     * Resolve function.
     *
     * @param  Field                                                      $field
     * @param  ContextInterface $context
     * @param  ResolveInfo                                                $info
     * @param  array|null                                                 $value
     * @param  array|null                                                 $args
     * @return bool|false|Value|mixed|string|null
     * 
     * @throws LocalizedException
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function resolve(
        Field $field,
        $context,
        ResolveInfo $info,
        array $value = null,
        array $args = null
    ) {
        if (false === $context->getExtensionAttributes()->getIsCustomer()) {
            throw new GraphQlAuthorizationException(
                __('The request is allowed for logged in customer')
            );
        }
        if (empty($args['input']['category_id']) || empty($args['input']['customer_email'])) {
            throw new GraphQlInputException(
                __('Required parameters is missing')
            );
        }
        try {
            $currentUserId = $context->getUserId();
            $this->serviceLocationValidator->isServiceAvailable(
                $args['input']['district_code']
            );
            $data = $this->zoomMeetingProvider->getMeeting(
                $currentUserId,
                $args['input']
            );

            return $data;
        } catch (\Exception $e) {
            throw new GraphQlInputException(
                __($e->getMessage())
            );
        }
    }
}

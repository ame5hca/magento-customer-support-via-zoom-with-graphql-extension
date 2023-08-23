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

use AmeshExtensions\CustomerSupport\Model\CallbackRequestManager;

/**
 * Class CallbackRequest
 *
 * @package AmeshExtensions\CustomerSupportGraphQl\Model\Resolver
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class CallbackRequest implements ResolverInterface
{
    private $callbackRequestManager;

    public function __construct(
        CallbackRequestManager $callbackRequestManager
    ) {
        $this->callbackRequestManager = $callbackRequestManager;
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
                __('Required parameters missing')
            );
        }
        try {
            $currentUserId = $context->getUserId();
            $requestData = $args['input'];
            $requestData['customer_id'] = $currentUserId;
            $requestData['status'] = 'requested';
            $this->callbackRequestManager->checkExistingCallbackRequest(
                $currentUserId, 
                $requestData['category_id']
            );
            return $this->callbackRequestManager->saveRequest($requestData);
        } catch (\Exception $e) {
            throw new GraphQlInputException(
                __($e->getMessage())
            );            
        }
        
    }
}

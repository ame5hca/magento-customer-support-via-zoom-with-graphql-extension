<?php

declare(strict_types=1);

namespace AmeshExtensions\CustomerSupportGraphQl\Model\Resolver;

use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\Resolver\ContextInterface;
use Magento\Framework\GraphQl\Query\Resolver\Value;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use AmeshExtensions\PincodeChecker\Model\ResourceModel\Pincode\CollectionFactory;

class CustomerSupportDistricts implements ResolverInterface
{
    private $deliveryCollectionFactory;

    public function __construct(
        CollectionFactory $deliveryCollectionFactory
    ) {
        $this->deliveryCollectionFactory = $deliveryCollectionFactory;
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
        try {
            $districtInfo = [];
            $pincodeListCollection = $this->deliveryCollectionFactory->create();
            $pincodeListCollection->addFieldToSelect(
                ['district']
            );

            $pincodeListCollection->addFieldToFilter("district",["nin"=>["Kozhikode","Thrissur"]]);

            $pincodeListCollection->addFieldToFilter(
                'status',
                ['eq' => 1]
            );
            $pincodeListCollection->getSelect()->group('district');
            if ($pincodeListCollection->getSize() > 0) {
                foreach ($pincodeListCollection as $deliveryArea) {
                    $districtInfo[] = [
                        'code' => $deliveryArea->getDistrict(),
                        'name' => $deliveryArea->getDistrict()
                    ];
                }
            }

            return $districtInfo;
        } catch (\Exception $e) {
            throw new GraphQlInputException(
                __($e->getMessage())
            );
        }
    }
}

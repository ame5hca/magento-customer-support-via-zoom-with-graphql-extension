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
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory;

class CustomerSupportCategories implements ResolverInterface
{
    private $categoryCollectionFactory;

    public function __construct(
        CollectionFactory $categoryCollectionFactory
    ) {
        $this->categoryCollectionFactory = $categoryCollectionFactory;
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
            $categoryInfo = [];
            $categoryCollection = $this->categoryCollectionFactory->create();
            $categoryCollection->addFieldToSelect(['id', 'name']);
            $categoryCollection->addFieldToFilter('customer_support', ['eq' => 1]);
            if ($categoryCollection->getSize() > 0) {
                foreach ($categoryCollection as $category) {
                    $categoryInfo[] = [
                        'id' => $category->getId(),
                        'name' => $category->getName()
                    ];
                }
            }
            return $categoryInfo;
        } catch (\Exception $e) {
            throw new GraphQlInputException(
                __($e->getMessage())
            );
        }
    }
}

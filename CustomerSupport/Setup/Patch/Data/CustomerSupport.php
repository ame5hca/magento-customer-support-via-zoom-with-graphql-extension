<?php

namespace AmeshExtensions\CustomerSupport\Setup\Patch\Data;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Catalog\Setup\CategorySetupFactory;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Catalog\Model\Category;
use Magento\Eav\Model\Entity\Attribute\Source\Boolean;
use Zend_Validate_Exception;

class CustomerSupport implements DataPatchInterface
{
    const CATEGORY_CUSTOMER_SUPPORT_ATTRIBUTE_CODE = 'customer_support';

    /**
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;

    /**
     * @var CategorySetupFactory
     */
    private $categorySetupFactory;

    /**
     * CategoryTopInfoBlockAttribute constructor.
     *
     * @param  ModuleDataSetupInterface $moduleDataSetup
     * @param  CategorySetupFactory     $categorySetupFactory
     * @return void
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        CategorySetupFactory $categorySetupFactory
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->categorySetupFactory = $categorySetupFactory;
    }

    /**
     * Attribute create function.
     *
     * @return DataPatchInterface|void
     * @throws LocalizedException
     * @throws Zend_Validate_Exception
     */
    public function apply()
    {
        $categorySetup = $this->categorySetupFactory->create(
            ['setup' => $this->moduleDataSetup]
        );
        
        $categorySetup->addAttribute(
            Category::ENTITY,
            self::CATEGORY_CUSTOMER_SUPPORT_ATTRIBUTE_CODE,
            [
                'type' => 'int',
                'label' => 'Enable For Customer Support',
                'input' => 'boolean',
                'source' => Boolean::class,
                'default'  => '0',
                'required' => false,
                'visible' => true,
                'sort_order' => 10,
                'global' => ScopedAttributeInterface::SCOPE_STORE,
                'group' => 'More Information'
            ]
        );
    }

    /**
     * Get the dependencies.
     *
     * @return array|string[]
     */
    public static function getDependencies()
    {
        /**
         * This is dependency to another patch. Dependency should be applied first
         * One patch can have few dependencies
         * Patches do not have versions, so if in old approach with Install/Ugrade data scripts you used
         * versions, right now you need to point from patch with higher version to patch with lower version
         * But please, note, that some of your patches can be independent and can be installed in any sequence
         * So use dependencies only if this important for you
         */
        return [];
    }

    /**
     * Get the aliases.
     *
     * @return array|string[]
     */
    public function getAliases()
    {
        /**
         * This internal Magento method, that means that some patches with time can change their names,
         * but changing name should not affect installation process, that's why if we will change name of the patch
         * we will add alias here
         */
        return [];
    }
}

<?php

namespace AmeshExtensions\CustomerSupport\Plugin\Block\Adminhtml\User\Edit\Tab;

use Magento\User\Block\User\Edit\Tab\Main;
use Magento\Catalog\Model\Layer\Resolver;
use Magento\Catalog\Model\CategoryFactory;
use AmeshExtensions\CustomerSupport\Model\MeetingAgentFactory;
use Magento\Framework\App\Request\Http;

class MainPlugin
{
    /**
     * @var Resolver
     */
    protected $layerResolver;

    /**
     * @var CategoryFactory
     */
    protected $categoryFactory;

    private $meetingAgentFactory;

    protected $request;

    public function __construct(
        Resolver $layerResolver,
        CategoryFactory $categoryFactory,
        Http $request,
        MeetingAgentFactory $meetingAgentFactory
    ) {
        $this->layerResolver = $layerResolver;
        $this->categoryFactory = $categoryFactory;
        $this->meetingAgentFactory = $meetingAgentFactory;
        $this->request = $request;
    }

    public function aroundGetFormHtml(
        Main $subject,
        \Closure $proceed
    ) {
        $form = $subject->getForm();
        if (is_object($form)) {
            $fieldset = $form->addFieldset(
                'admin_user_customersupport',
                ['legend' => __('Customer Support Information')]
            );

            $fieldset->addField(
                'is_agent',
                'select',
                [
                    'name' => 'is_agent',
                    'label' => __('Is Agent'),
                    'id' => 'is_agent',
                    'title' => __('Is Agent'),
                    'values' => [
                        ['label' => 'No', 'value' => 0],
                        ['label' => 'Yes', 'value' => 1]
                    ],
                    'required' => false
                ]
            );
            $fieldset->addField(
                'primary_category_id',
                'select',
                [
                    'name' => 'primary_category_id',
                    'label' => __('Primary Category'),
                    'id' => 'primary_category_id',
                    'title' => __('Primary Category'),
                    'values' => $this->getCategoriesTree(2),
                    'required' => true
                ]
            );
            $fieldset->addField(
                'secondary_category_id',
                'select',
                [
                    'name' => 'secondary_category_id',
                    'label' => __('Secondary Category'),
                    'id' => 'secondary_category_id',
                    'title' => __('Secondary Category'),
                    'values' => $this->getCategoriesTree(2),
                    'required' => false
                ]
            );

            $fieldset->addField(
                'zoom_user_id',
                'text',
                [
                    'name' => 'zoom_user_id',
                    'label' => __('Zoom User Email'),
                    'id' => 'zoom_user_id',
                    'title' => __('Zoom User Email'),
                    'required' => false
                ]
            );

            $adminId = $this->request->getParam('user_id', null);
            if ($adminId != null) {
                $agentInfo = $this->getAgentInfo($adminId);
                $form->addValues(
                    [
                        'primary_category_id' => $agentInfo['primary_category_id'],
                        'secondary_category_id' => $agentInfo['secondary_category_id'],
                        'zoom_user_id' => $agentInfo['zoom_user_id'],
                        'is_agent' => $agentInfo['is_agent'],
                     ]
                );
            }
            $subject->setForm($form);
        }

        return $proceed();
    }

    public function getCategoriesTree()
    {
        $categoryTree = [];
        $categoryCollection = $this->categoryFactory->create()->getCollection();
        $categoryCollection->addFieldToSelect(['name'])
            ->addFieldToFilter('is_active', ['eq' => '1'])
            ->addFieldToFilter('customer_support', ['eq' => '1']);           
        
        foreach ($categoryCollection as $category) {
            $categoryTree[] = [
                'label' => $category->getName(), 
                'value' => $category->getId()
            ];
        }

        return $categoryTree;
    }

    private function getAgentInfo($adminId)
    {
        $meetingAgentModel = $this->meetingAgentFactory->create();
        $meetingAgent = $meetingAgentModel->load($adminId, 'admin_id');
        if ($meetingAgent->getId()) {
            return $meetingAgent->getData();
        }
        return null;
    }
}

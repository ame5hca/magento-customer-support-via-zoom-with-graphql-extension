<?php

namespace AmeshExtensions\CustomerSupport\Ui\Component\Listing\Column\CallbackRequests;

use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\UrlInterface;

class Actions extends Column
{
    const URL_PATH_EDIT = 'customersupport/callbackrequests/edit';

    protected $urlBuilder;

    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        array $components = [],
        array $data = []
    ) {
        $this->urlBuilder = $urlBuilder;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                if (isset($item['entity_id'])) {
                    $copyUrl = $this->urlBuilder->getUrl(
                        'video-support', 
                        ['agent_id' => $item['agent_id']]
                    );
                    $item[$this->getData('name')]['edit'] = [
                        'href' => $this->urlBuilder->getUrl(
                            static::URL_PATH_EDIT,
                            [
                                'id' => $item['entity_id']
                            ]
                        ),
                        'label' => __('Edit')
                    ];
                    if ($item['status'] != 'requested') {
                        $item[$this->getData('name')]['view'] = [
                            'id' => 'callbackrequest_copy_url_link', 
                            'href' => 'javascript:void(0)',
                            'click' => 'copyToClipboard(\'' . $copyUrl . '\')',
                            'label' => __('Copy Url')
                        ];
                    }                    
                }
            }
        }
        
        return $dataSource;
    }
}
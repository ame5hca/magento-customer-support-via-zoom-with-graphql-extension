<?php

namespace AmeshExtensions\CustomerSupport\Model\Zoom\DataProvider;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class ConfigProvider
{
    const XML_PATH_ZOOM_MODULE_STATUS = 'customersupport/zoom/enable';

    const XML_PATH_ZOOM_API_KEY = 'customersupport/zoom/api_key';

    const XML_PATH_ZOOM_API_SECRET = 'customersupport/zoom/api_secret';

    const XML_PATH_ZOOM_EVENT_VERIFY_TOKEN = 'customersupport/zoom/event_notification_token';

    const XML_PATH_SEERVICE_DISTRICTS = 'customersupport/service_location_details/locations';

    protected $scopeConfig;

    public function __construct(
        ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    public function isModuleEnalbed()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_ZOOM_MODULE_STATUS,
            ScopeInterface::SCOPE_STORE
        );
    }

    public function getApiKey()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_ZOOM_API_KEY,
            ScopeInterface::SCOPE_STORE
        );
    }

    public function getApiSecret()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_ZOOM_API_SECRET,
            ScopeInterface::SCOPE_STORE
        );
    }

    public function getEventNotificationVerifyToken()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_ZOOM_EVENT_VERIFY_TOKEN,
            ScopeInterface::SCOPE_STORE
        );
    }

    public function getServiceLocations()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_SEERVICE_DISTRICTS,
            ScopeInterface::SCOPE_STORE
        );
    }
}
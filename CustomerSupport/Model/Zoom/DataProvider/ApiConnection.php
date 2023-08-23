<?php

namespace AmeshExtensions\CustomerSupport\Model\Zoom\DataProvider;

use AmeshExtensions\CustomerSupport\Logger\Zoom\Logger;
use Firebase\JWT\JWT;

class ApiConnection
{
    /**
     * Jwt App client id
     */
    const CLIENT_ID = 'xxxxxxxxxxxxxxxxxxxxxxx';

    /**
     * Jwt App client secret id
     */
    const CLIENT_SECRETE = 'xxxxxxxxxxxxxxxxxxxxxxxxx';

    /**
     * Zoom api base url
     */
    const API_BASE_URL = 'https://api.zoom.us';

    protected $jwtProvider;

    protected $configProvider;

    public function __construct(
        JWT $jwtProvider,
        ConfigProvider $configProvider
    ) {
        $this->jwtProvider = $jwtProvider;
        $this->configProvider = $configProvider;
    }

    /**
     * Genrating a Jwt token for the zoom api authentication.
     * For the security purpose the token expiry is set to a
     * shorter time.
     *
     * @return void
     */
    public function generateJwtToken()
    {
        $tokenData = [
            "iss" => $this->configProvider->getApiKey(),
            "exp" => time() + 3600 //60 seconds as suggested
        ];

        return $this->jwtProvider->encode(
            $tokenData,
            $this->configProvider->getApiSecret()
        );
    }
}

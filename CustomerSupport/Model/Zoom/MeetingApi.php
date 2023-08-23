<?php

namespace AmeshExtensions\CustomerSupport\Model\Zoom;

use AmeshExtensions\CustomerSupport\Logger\Zoom\Logger;
use AmeshExtensions\CustomerSupport\Model\Zoom\DataProvider\ApiConnection;
use AmeshExtensions\CustomerSupport\Model\Zoom\ServiceProvider\MeetingApiConfigManager;
use Magento\Framework\Exception\LocalizedException;

class MeetingApi
{
    protected $apiConnectionProvider;

    protected $meetingApiConfigManager;

    private $logger;

    public function __construct(
        ApiConnection $apiConnectionProvider,
        MeetingApiConfigManager $meetingApiConfigManager,
        Logger $logger
    ) {
        $this->apiConnectionProvider = $apiConnectionProvider;
        $this->meetingApiConfigManager = $meetingApiConfigManager;
        $this->logger = $logger;
    }

    public function createNewMeeting()
    {
        $token = $this->apiConnectionProvider->generateJwtToken();
        $client = new \GuzzleHttp\Client(
            ['base_uri' => ApiConnection::API_BASE_URL]
        );

        try {
            $userEmail = $this->meetingApiConfigManager->getAgentEmail();
            $configns = $this->meetingApiConfigManager->getConfigns();
            $response = $client->request(
                'POST',
                '/v2/users/'.$userEmail.'/meetings',
                [
                    "headers" => [
                        "Authorization" => "Bearer " . $token
                    ],
                    'json' => $configns,
                ]
            );
            $data = json_decode($response->getBody());
            $updatedData = (array) $data;
            $updatedData['agent_id'] = $this->meetingApiConfigManager->getAgentId();
            $this->meetingApiConfigManager->clear();
            
            return $updatedData;

        } catch (LocalizedException $le) {
            $this->logger->info('CreateMeetingError: ' . $le->getMessage());
            throw new LocalizedException(__($le->getMessage()));
        } catch (\Exception $e) {
            $retryCount = 1;
            if (401 == $e->getCode()) {
                do {
                    $this->createNewMeeting();
                    $retryCount++;
                } while ($retryCount == 2);
            }
            $this->logger->info('CreateMeetingError: ' . $e->getMessage());
            throw new LocalizedException(__($e->getMessage()));
        }
    }

    public function getMeetingParticipants($meetingId)
    {
        $token = $this->apiConnectionProvider->generateJwtToken();
        $client = new \GuzzleHttp\Client(
            ['base_uri' => ApiConnection::API_BASE_URL]
        );

        try {
            $response = $client->request(
                'GET',
                '/v2/metrics/meetings/'.$meetingId.'/participants/',
                [
                    "headers" => [
                        "Authorization" => "Bearer " . $token
                    ]
                ]
            );
            $data = json_decode($response->getBody());
            
            return $data;

        } catch (LocalizedException $le) {
            $this->logger->info('CreateMeetingError: ' . $le->getMessage());
            throw new LocalizedException(__($le->getMessage()));
        } catch (\Exception $e) {            
            $this->logger->info('CreateMeetingError: ' . $e->getMessage());
            throw new LocalizedException(__($e->getMessage()));
        }
    }

    public function getMeetingInfo($meetingId)
    {
        $token = $this->apiConnectionProvider->generateJwtToken();
        $client = new \GuzzleHttp\Client(
            ['base_uri' => ApiConnection::API_BASE_URL]
        );

        try {
            $response = $client->request(
                'GET',
                '/v2/meetings/'.$meetingId,
                [
                    "headers" => [
                        "Authorization" => "Bearer " . $token
                    ]
                ]
            );
            if ($response->getStatusCode() != 200) {
                throw new LocalizedException(
                    __('Status Code = '.$response->getStatusCode())
                );
                
            }
            $data = json_decode($response->getBody());
            
            return (array) $data;

        } catch (LocalizedException $le) {
            $this->logger->info('GetMeetingError: ' . $le->getMessage());
            throw new LocalizedException(__($le->getMessage()));
        } catch (\Exception $e) {            
            $this->logger->info('GetMeetingError: ' . $e->getMessage());
            throw new LocalizedException(__($e->getMessage()));
        }
    }


    public function getActiveMeetingsOfUser($userId)
    {
        $token = $this->apiConnectionProvider->generateJwtToken();
        $client = new \GuzzleHttp\Client(
            ['base_uri' => ApiConnection::API_BASE_URL]
        );

        try {
            $response = $client->request(
                'GET',
                '/v2/users/'.$userId.'/meetings',
                [
                    "headers" => [
                        "Authorization" => "Bearer " . $token
                    ],
                    'json' => [
                        'type' => 'live'
                    ],
                ]
            );
            $data = json_decode($response->getBody());
            $updatedData = (array) $data;
            
            return $updatedData;

        } catch (LocalizedException $le) {
            $this->logger->info('ActiveMeetingStopError: ' . $le->getMessage());
            throw new LocalizedException(__($le->getMessage()));
        } catch (\Exception $e) {            
            $this->logger->info('ActiveMeetingStopError: ' . $e->getMessage());
            throw new LocalizedException(__($e->getMessage()));
        }
    }

    public function endMeeting($meetingId)
    {
        $token = $this->apiConnectionProvider->generateJwtToken();
        $client = new \GuzzleHttp\Client(
            ['base_uri' => ApiConnection::API_BASE_URL]
        );

        try {
            $response = $client->request(
                'PUT',
                '/v2/meetings/'.$meetingId.'/status',
                [
                    "headers" => [
                        "Authorization" => "Bearer " . $token
                    ],
                    'json' => [
                        'action' => 'end'
                    ],
                ]
            );
            
            return $response->getStatusCode();

        } catch (LocalizedException $le) {
            $this->logger->info('ActiveMeetingStopError: ' . $le->getMessage());
            throw new LocalizedException(__($le->getMessage()));
        } catch (\Exception $e) {            
            $this->logger->info('ActiveMeetingStopError: ' . $e->getMessage());
            throw new LocalizedException(__($e->getMessage()));
        }
    }

    public function updateMeeting($meetingId, $data)
    {
        $token = $this->apiConnectionProvider->generateJwtToken();
        $client = new \GuzzleHttp\Client(
            ['base_uri' => ApiConnection::API_BASE_URL]
        );

        try {
            $response = $client->request(
                'PATCH',
                '/v2/meetings/'.$meetingId,
                [
                    "headers" => [
                        "Authorization" => "Bearer " . $token
                    ],
                    'json' => $data,
                ]
            );

            return $response->getStatusCode();

        } catch (LocalizedException $le) {
            $this->logger->info('UpdateMeetingError: ' . $le->getMessage());
            throw new LocalizedException(__($le->getMessage()));
        } catch (\Exception $e) {            
            $this->logger->info('UpdateMeetingError: ' . $e->getMessage());
            throw new LocalizedException(__($e->getMessage()));
        }
    }

    public function listUsers()
    {
        $token = $this->apiConnectionProvider->generateJwtToken();
        $client = new \GuzzleHttp\Client(
            ['base_uri' => ApiConnection::API_BASE_URL]
        );

        try {
            $response = $client->request(
                'GET',
                '/v2/users',
                [
                    "headers" => [
                        "Authorization" => "Bearer " . $token
                    ],
                    'json' => [
                        'status' => 'active'
                    ],
                ]
            );
            $data = json_decode($response->getBody());
            $updatedData = (array) $data;
            if (!isset($updatedData['users']) || count($updatedData['users']) <0) {
                throw new LocalizedException(
                    __('No user list is returned')
                );
            }
                        
            return $updatedData;

        } catch (LocalizedException $le) {
            $this->logger->info('CreateMeetingError: ' . $le->getMessage());
            throw new LocalizedException(__($le->getMessage()));
        } catch (\Exception $e) {
            $retryCount = 1;
            if (401 == $e->getCode()) {
                do {
                    $this->createNewMeeting();
                    $retryCount++;
                } while ($retryCount == 2);
            }
            $this->logger->info('CreateMeetingError: ' . $e->getMessage());
            throw new LocalizedException(__($e->getMessage()));
        }
    }
}

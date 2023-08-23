<?php

namespace AmeshExtensions\CustomerSupport\Setup\Patch\Data;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use AmeshExtensions\CustomerSupport\Model\Zoom\MeetingApi;
use AmeshExtensions\CustomerSupport\Model\ZoomHostUsersFactory;
use Zend_Validate_Exception;

class FetchAndUpdateHostUsersInfo implements DataPatchInterface
{
    private $meetingApi;

    private $zoomHostUsersFactory;

    public function __construct(
        MeetingApi $meetingApi,
        ZoomHostUsersFactory $zoomHostUsersFactory
    ) {
        $this->meetingApi = $meetingApi;
        $this->zoomHostUsersFactory = $zoomHostUsersFactory;
    }

    /**
     * Fetch and save zoom meetying host user info from zoom account.
     * This is used to skip the host user updation while a participant/host
     * joined notification event
     *
     * @return DataPatchInterface|void
     * @throws LocalizedException
     * @throws Zend_Validate_Exception
     */
    public function apply()
    {   
        try {
            $usersData = $this->meetingApi->listUsers();

            foreach ($usersData['users'] as $user) {
                $zoomUserModel = $this->zoomHostUsersFactory->create();
                $zoomUserModel->setUserId($user->id);
                $zoomUserModel->setFirstName($user->first_name);
                $zoomUserModel->setLastName($user->last_name);
                $zoomUserModel->setEmail($user->email);
                $zoomUserModel->setType($user->type);
                $zoomUserModel->setStatus($user->status);
                $zoomUserModel->save();
            }
        } catch (\Exception $e) {
            throw new LocalizedException(
                __($e->getMessage())
            );
            
        }        
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

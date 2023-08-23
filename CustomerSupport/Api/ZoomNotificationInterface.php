<?php

namespace AmeshExtensions\CustomerSupport\Api;

interface ZoomNotificationInterface
{
    /**
     * Get the zoom meeting notifications
     * from the zoom server.
     *     
     * @return string
     */
    public function getNotifications();
}
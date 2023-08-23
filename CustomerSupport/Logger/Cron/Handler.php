<?php

namespace AmeshExtensions\CustomerSupport\Logger\Cron;

use Monolog\Logger;
use Magento\Framework\Logger\Handler\Base as BaseHandler;

/**
 * Class Handler
 *
 * @package AmeshExtensions\CustomerSupport\Logger\Cron
 */
class Handler extends BaseHandler
{
    /**
     * Logging level
     *
     * @var int
     */
    protected $loggerType = Logger::INFO;

    /**
     * File name
     *
     * @var string
     */
    protected $fileName = '/var/log/customersupport_cron.log';
}
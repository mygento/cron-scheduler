<?php

/**
 * @author Mygento Team
 * @copyright 2019-2022 Mygento (https://www.mygento.ru)
 * @package Mygento_CronScheduler
 */

namespace Mygento\CronScheduler\Cron;

use Mygento\CronScheduler\Api\ScheduleManagementInterface;
use Mygento\CronScheduler\Helper\Data;
use Psr\Log\LoggerInterface;

/**
 * Class DropRunningJobs
 * @package Mygento\CronScheduler\Cron
 */
class DropRunningJobs
{
    /**
     * @var ScheduleManagementInterface
     */
    private $scheduleManagement;

    /**
     * @var Data
     */
    private $cronSchedulerHelper;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * DropRunningJobs constructor.
     * @param ScheduleManagementInterface $scheduleManagement
     * @param Data $cronSchedulerHelper
     * @param LoggerInterface $logger
     */
    public function __construct(
        ScheduleManagementInterface $scheduleManagement,
        Data $cronSchedulerHelper,
        LoggerInterface $logger
    ) {
        $this->scheduleManagement = $scheduleManagement;
        $this->cronSchedulerHelper = $cronSchedulerHelper;
        $this->logger = $logger;
    }

    /**
     * Execute
     *
     * @return void
     */
    public function execute()
    {
        if (!$this->cronSchedulerHelper->isDropRunningJobs()) {
            return;
        }

        try {
            $this->scheduleManagement->dropExceededRunningJobs();
        } catch (\Exception $e) {
            $this->logger->critical($e->getMessage());
        }
    }
}

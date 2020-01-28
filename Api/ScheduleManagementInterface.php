<?php

/**
 * @author Mygento Team
 * @copyright 2019 Mygento (https://www.mygento.ru)
 * @package Mygento_CronScheduler
 */

namespace Mygento\CronScheduler\Api;

/**
 * Interface ScheduleManagementInterface
 * @package Mygento\CronScheduler\Api
 */
interface ScheduleManagementInterface
{
    /**
     * Drop exceeded running jobs
     *
     * @return void
     * @throws \Exception
     */
    public function dropExceededRunningJobs();
}

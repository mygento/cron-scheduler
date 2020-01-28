<?php

/**
 * @author Mygento Team
 * @copyright 2019-2020 Mygento (https://www.mygento.ru)
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
     * @throws \Exception
     * @return void
     */
    public function dropExceededRunningJobs();
}

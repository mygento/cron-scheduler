<?php

/**
 * @author Mygento Team
 * @copyright 2019 Mygento (https://www.mygento.ru)
 * @package Mygento_CronScheduler
 */

namespace Mygento\CronScheduler\Helper;

/**
 * Class Data
 * @package Mygento\CronScheduler\Helper
 */
class Data extends \Mygento\Base\Helper\Data
{
    const XML_PATH_GENERAL_IS_DROP_RUNNING_JOBS = 'cron_scheduler/general/is_drop_running_jobs';
    const XML_PATH_GENERAL_RUNNING_JOBS_TIMEOUT = 'cron_scheduler/general/running_jobs_timeout';

    /**
     * @var string
     */
    protected $code = 'cron_scheduler';

    /**
     * Is drop running jobs
     *
     * @return bool
     */
    public function isDropRunningJobs()
    {
        return $this->scopeConfig->isSetFlag(self::XML_PATH_GENERAL_IS_DROP_RUNNING_JOBS);
    }

    /**
     * Get running jobs timeout
     *
     * @return int
     */
    public function getRunningJobsTimeout()
    {
        return (int) $this->scopeConfig->getValue(self::XML_PATH_GENERAL_RUNNING_JOBS_TIMEOUT);
    }
}

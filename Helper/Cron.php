<?php

/**
 * @author Mygento Team
 * @copyright 2019-2022 Mygento (https://www.mygento.ru)
 * @package Mygento_CronScheduler
 */

namespace Mygento\CronScheduler\Helper;

class Cron extends \Magento\Framework\App\Helper\AbstractHelper
{
    public const JOB = 'cronscheduler_job';

    private $cron;

    public function __construct(
        \Mygento\Base\Helper\Cron $cron,
        \Magento\Framework\App\Helper\Context $context
    ) {
        parent::__construct($context);

        $this->cron = $cron;
    }

    public function getCronStatus()
    {
        $status = $this->cron->getLastUpdate(self::JOB);
        //TODO: ADD MESSAGE;
    }
}

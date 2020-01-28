<?php

/**
 * @author Mygento Team
 * @copyright 2019 Mygento (https://www.mygento.ru)
 * @package Mygento_CronScheduler
 */

namespace Mygento\CronScheduler\Model;

use Magento\Cron\Model\ResourceModel\Schedule\Collection as ScheduleCollection;
use Magento\Cron\Model\ResourceModel\Schedule\CollectionFactory as ScheduleCollectionFactory;
use Magento\Cron\Model\Schedule;
use Magento\Framework\Stdlib\DateTime;
use Mygento\CronScheduler\Api\ScheduleManagementInterface;
use Mygento\CronScheduler\Helper\Data;
use Psr\Log\LoggerInterface;

class ScheduleManagement implements ScheduleManagementInterface
{
    /**
     * @var Data
     */
    private $cronSchedulerHelper;

    /**
     * @var ScheduleCollectionFactory
     */
    private $scheduleCollectionFactory;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * ScheduleManagement constructor.
     * @param Data $cronSchedulerHelper
     * @param ScheduleCollectionFactory $scheduleCollectionFactory
     * @param LoggerInterface $logger
     */
    public function __construct(
        Data $cronSchedulerHelper,
        ScheduleCollectionFactory $scheduleCollectionFactory,
        LoggerInterface $logger
    ) {
        $this->cronSchedulerHelper = $cronSchedulerHelper;
        $this->scheduleCollectionFactory = $scheduleCollectionFactory;
        $this->logger = $logger;
    }

    /**
     * Drop exceeded running jobs
     *
     * @return void
     * @throws \Exception
     */
    public function dropExceededRunningJobs()
    {
        if (!$this->cronSchedulerHelper->getRunningJobsTimeout()) {
            return;
        }

        /** @var ScheduleCollection $scheduleCollection */
        $scheduleCollection = $this->scheduleCollectionFactory->create();
        $toDate = (new \DateTime(sprintf('-%s minutes', $this->cronSchedulerHelper->getRunningJobsTimeout())))
            ->format(DateTime::DATETIME_PHP_FORMAT);
        $scheduleCollection
            ->addFieldToFilter('status', Schedule::STATUS_RUNNING)
            ->addFieldToFilter('scheduled_at', ['lt' => $toDate]);

        /** @var Schedule $schedule */
        foreach ($scheduleCollection as $schedule) {
            try {
                $schedule
                    ->setStatus(Schedule::STATUS_ERROR)
                    ->setMessages(sprintf(
                        'Cron Job %s is dropped by Mygento Cron Scheduler at %s',
                        $schedule->getJobCode(),
                        (new \DateTime())->format(DateTime::DATETIME_PHP_FORMAT)
                    ))
                    ->save();
            } catch (\Exception $e) {
                $this->logger->critical($e);
            }
        }
    }
}

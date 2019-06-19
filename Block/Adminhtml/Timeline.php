<?php

/**
 * @author Mygento Team
 * @copyright 2019 Mygento (https://www.mygento.ru)
 * @package Mygento_CronScheduler
 */

namespace Mygento\CronScheduler\Block\Adminhtml;

class Timeline extends \Magento\Backend\Block\Template
{
    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    private $datetime;

    /**
     * @var \Magento\Cron\Model\ResourceModel\Schedule\CollectionFactory
     */
    private $collectionFactory;

    public function __construct(
        \Magento\Framework\Stdlib\DateTime\DateTime $datetime,
        \Magento\Cron\Model\ResourceModel\Schedule\CollectionFactory $collectionFactory,
        \Magento\Backend\Block\Template\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->collectionFactory = $collectionFactory;
        $this->datetime = $datetime;
    }

    public function getJobData(): array
    {
        $data = [];
        $schedules = $this->collectionFactory->create();
        $schedules->getSelect()->order('job_code');

        foreach ($schedules as $schedule) {
            $start = $schedule->getExecutedAt();
            $end = $schedule->getFinishedAt();
            $status = $schedule->getStatus();

            if ($start == null) {
                $start = $schedule->getScheduledAt();
                $end = $schedule->getScheduledAt();
            }

            if ($status == \Magento\Cron\Model\Schedule::STATUS_RUNNING) {
                $end = $this->datetime->date('Y-m-d H:i:s');
            }

            if ($status == \Magento\Cron\Model\Schedule::STATUS_ERROR && $end == null) {
                $end = $start;
            }

            $tooltip = '';

            $data[] = [
                $schedule->getJobCode(),
                $status,
                $this->getStatusLevel($status),
                $tooltip,
                $this->formatDateForJs($start),
                $this->formatDateForJs($end),
                //$schedule->getScheduleId(),
            ];
        }

        return $data;
    }

    public function getJobCount(): int
    {
        $schedules = $this->collectionFactory->create();
        $schedules->getSelect()->group('job_code')->order('job_code');

        return $schedules->getSize();
    }

    /**
     * Get Status Level
     * @param $status
     * @return string
     */
    private function getStatusLevel($status): string
    {
        switch ($status) {
            case \Magento\Cron\Model\Schedule::STATUS_MISSED:
                $level = 'f75300';
                break;
            case \Magento\Cron\Model\Schedule::STATUS_ERROR:
                $level = 'ff0000';
                break;
            case \Magento\Cron\Model\Schedule::STATUS_RUNNING:
                $level = '0000ff';
                break;
            case \Magento\Cron\Model\Schedule::STATUS_PENDING:
                $level = 'a9a9a9';
                break;
            case \Magento\Cron\Model\Schedule::STATUS_SUCCESS:
                $level = '36b963';
                break;
            default:
                $level = '000000';
        }

        return $level;
    }

    /**
     * Generate js date format for given date
     * @param $date
     * @return string
     */
    private function formatDateForJs($date)
    {
        return 'new Date(' . $this->datetime->date('Y,', $date)
            . ($this->datetime->date('m', $date) - 1)
            . $this->datetime->date(',d,H,i,s,0', $date) . ')';
    }
}

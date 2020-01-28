<?php

/**
 * @author Mygento Team
 * @copyright 2019-2020 Mygento (https://www.mygento.ru)
 * @package Mygento_CronScheduler
 */

namespace Mygento\CronScheduler\Block\Adminhtml;

class Timeline extends \Magento\Backend\Block\Template
{
    const ZOOM = 15;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    private $datetime;

    /**
     * @var int starttime
     */
    private $starttime;

    /**
     * @var int endtime
     */
    private $endtime;

    /**
     * @var array schedules
     */
    private $schedules = [];

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

    /**
     * Get all available job codes
     *
     * @return array
     */
    public function getAvailableJobCodes()
    {
        $this->getJobData();

        return array_keys($this->schedules);
    }

    public function getTimelinePanelWidth()
    {
        return 1 + ($this->endtime - $this->starttime) / self::ZOOM;
    }

    public function getStarttime()
    {
        return $this->starttime;
    }

    public function getNowline()
    {
        return (time() - $this->starttime) / self::ZOOM;
    }

    public function getEndtime()
    {
        return $this->endtime;
    }

    public function decorateTime($value, $echoToday = false)
    {
        $result = $this->datetime->date('Y-m-d H:i', $value);
        $replace = [
            $this->datetime->date('Y-m-d ', time()) => $echoToday ? __('Today') : '',
            $this->datetime->date('Y-m-d ', strtotime('+1 day')) => __('Tomorrow') . ', ',
            $this->datetime->date('Y-m-d ', strtotime('-1 day')) => __('Yesterday') . ', ',
        ];

        return str_replace(array_keys($replace), array_values($replace), $result);
    }

    /**
     * Get schedules for given code
     *
     * @param string $code
     * @return array
     */
    public function getSchedulesForCode($code)
    {
        return $this->schedules[$code];
    }

    public function getJobData()
    {
        $schedules = $this->collectionFactory->create();
        $schedules->getSelect()->order('job_code');

        $minDate = null;
        $maxDate = null;

        foreach ($schedules as $schedule) {
            $start = $schedule->getExecutedAt();

            $minDate = is_null($minDate) ? $start : min($minDate, $start);
            $maxDate = is_null($maxDate) ? $start : max($maxDate, $start);
            $this->schedules[$schedule->getJobCode()][] = $schedule;
        }

        $this->starttime = $this->hourFloor(strtotime($minDate));
        $this->endtime = $this->hourCeil(strtotime($maxDate));
    }

    /**
     * Get attributes for div representing a gantt element
     *
     * @param $schedule
     * @return string
     */
    public function getScheduleResult($schedule)
    {
        if ($schedule->getStatus() == \Magento\Cron\Model\Schedule::STATUS_RUNNING) {
            $duration = time() - strtotime($this->getStart($schedule));
        } else {
            $duration = $this->getDuration($schedule) ? $this->getDuration($schedule) : 0;
        }
        $duration = $duration / self::ZOOM;
        $duration = ceil($duration / 4) * 4 - 1; // round to numbers dividable by 4, then remove 1 px border
        $duration = max($duration, 3);
        $offset = (strtotime($this->getStart($schedule)) - $this->starttime) / self::ZOOM;
        if ($offset < 0) { // cut bar
            $duration += $offset;
            $offset = 0;
        }
        $result = sprintf(
            '<div class="timeline-task timeline-task-%s" id="id_%s" style="width: %spx; left: %spx;" ></div>',
            $schedule->getStatus(),
            $schedule->getScheduleId(),
            $duration,
            $offset
        );
        if ($schedule->getStatus() == \Magento\Cron\Model\Schedule::STATUS_RUNNING) {
            $offset += $duration;
            $duration = strtotime($schedule->getEta()) - time();
            $duration = $duration / self::ZOOM;
            $result = sprintf(
                '<div class="timeline-estimation" style="width: %spx; left: %spx;" ></div>',
                $duration,
                $offset
            ) . $result;
        }

        return $result;
    }

    public function getOffset($schedule)
    {
        $offset = (strtotime($this->getStart($schedule)) - $this->starttime) / self::ZOOM;
        if ($offset < 0) { // cut bar
            $offset = 0;
        }

        return $offset;
    }

    /**
     * Return the last full houd
     *
     * @param int $timestamp
     * @return int
     */
    private function hourFloor($timestamp)
    {
        return mktime(date('H', $timestamp), 0, 0, date('n', $timestamp), date('j', $timestamp), date('Y', $timestamp));
    }

    /**
     * Returns the next full hour
     *
     * @param int $timestamp
     * @return int
     */
    private function hourCeil($timestamp)
    {
        return mktime(date('H', $timestamp) + 1, 0, 0, date('n', $timestamp), date('j', $timestamp), date('Y', $timestamp));
    }

    private function getDuration($shedule)
    {
        $duration = false;
        if ($shedule->getExecutedAt() && ($shedule->getExecutedAt() != '0000-00-00 00:00:00')) {
            if ($shedule->getFinishedAt() && ($shedule->getFinishedAt() != '0000-00-00 00:00:00')) {
                $time = strtotime($shedule->getFinishedAt());
            } elseif ($this->getStatus() == \Magento\Cron\Model\Schedule::STATUS_RUNNING) {
                $time = time();
            } else {
                return false;
            }
            $duration = $time - strtotime($shedule->getExecutedAt());
        }

        return $duration;
    }

    /**
     * Get start time (planned or actual)
     *
     * @param mixed $shedule
     * @return string
     */
    private function getStart($shedule)
    {
        $starttime = $shedule->getExecutedAt();
        if (empty($starttime) || $starttime == '0000-00-00 00:00:00') {
            $starttime = $shedule->getScheduledAt();
        }

        return $starttime;
    }
}

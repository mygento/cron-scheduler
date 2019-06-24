<?php

/**
 * @author Mygento Team
 * @copyright 2019 Mygento (https://www.mygento.ru)
 * @package Mygento_CronScheduler
 */

namespace Mygento\CronScheduler\Block\Adminhtml\Timeline;

class Detail extends \Magento\Backend\Block\Template
{
    protected $_template = 'Mygento_CronScheduler::timeline/details.phtml';

    public function __construct(
        \Magento\Framework\Stdlib\DateTime\DateTime $datetime,
        \Magento\Backend\Block\Template\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->datetime = $datetime;
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

    public function getScheduleDetail()
    {
        $zoom = \Mygento\CronScheduler\Block\Adminhtml\Timeline::ZOOM;
        $schedule = $this->getSchedule();
        $offset = -150 + (strtotime($this->getStart($schedule)) - $this->starttime) / $zoom;
        $result = sprintf(
            '<div class="timeline-details" style="left: %spx;">'
            . '<div class="timeline-headline timeline-headline-success"><h3>%s</h3></div>'
            . '<div class="timeline-content">'
            . '</div>',
            $offset,
            str_replace('_', ' ', $schedule->getJobCode())
        );

        return $result . '</div>';
    }

    protected function _toHtml()
    {
        if (!$this->getSchedule()) {
            return '';
        }

        return parent::_toHtml();
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

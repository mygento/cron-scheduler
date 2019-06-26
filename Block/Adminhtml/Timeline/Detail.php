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

    protected function _toHtml()
    {
        if (!$this->getSchedule()) {
            return '';
        }

        return parent::_toHtml();
    }
}

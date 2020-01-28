<?php

/**
 * @author Mygento Team
 * @copyright 2019-2020 Mygento (https://www.mygento.ru)
 * @package Mygento_CronScheduler
 */

namespace Mygento\CronScheduler\Ui\Component\Listing\Column;

class Status implements \Magento\Framework\Data\OptionSourceInterface
{
    /**
     * @var array
     */
    private $options;

    /**
     * @var \Mygento\CronScheduler\Model\ResourceModel\Schedule\CollectionFactory
     */
//    private $scheduleCollectionFactory;

//    public function __construct(
//        \Mygento\CronScheduler\Model\ResourceModel\Schedule\CollectionFactory $scheduleCollectionFactory
//    ) {
//        $this->scheduleCollectionFactory = $scheduleCollectionFactory->create();
//    }

    /**
     * Get all options available
     * @return array
     */
    public function toOptionArray()
    {
        if ($this->options === null) {
            $this->options = [];
//            $scheduleTaskStatuses = $this->scheduleCollectionFactory->getScheduleTaskStatuses();
//            foreach ($scheduleTaskStatuses as $scheduleTaskStatus) {
//                $status = $scheduleTaskStatus->getStatus();
//                $this->options[] = [
//                    'label' => __(strtoupper($status)),
//                    'value' => $status,
//                ];
//            }
        }

        return $this->options;
    }
}

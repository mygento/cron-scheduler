<?php

/**
 * @author Mygento Team
 * @copyright 2019 Mygento (https://www.mygento.ru)
 * @package Mygento_CronScheduler
 */

namespace Mygento\CronScheduler\Ui\DataProvider;

class Schedule extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    /**
     * @var \Magento\Cron\Model\ResourceModel\Schedule\Collection
     */
    protected $collection;

    /**
     * @param \Magento\Cron\Model\ResourceModel\Schedule\CollectionFactory $collection
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        \Magento\Cron\Model\ResourceModel\Schedule\CollectionFactory $collection,
        $name,
        $primaryFieldName,
        $requestFieldName,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->collection = $collection->create();
    }
}

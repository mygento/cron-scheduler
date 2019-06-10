<?php

/**
 * @author Mygento Team
 * @copyright 2019 Mygento (https://www.mygento.ru)
 * @package Mygento_CronScheduler
 */

namespace Mygento\CronScheduler\Ui\DataProvider;

class Job extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    /**
     * @var \Mygento\CronScheduler\Helper\Data
     */
    private $helper;

    /**
     * @var \Magento\Cron\Model\ConfigInterface
     */
    private $cronConfig;
    private $size;
    private $offset;

    public function __construct(
        \Mygento\CronScheduler\Helper\Data $helper,
        \Magento\Cron\Model\ConfigInterface $cronConfig,
        $name,
        $primaryFieldName,
        $requestFieldName,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->cronConfig = $cronConfig;
        $this->helper = $helper;
    }

    public function getData()
    {
        $data = [];
        $configJobs = $this->cronConfig->getJobs();

        foreach ($configJobs as $group => $jobs) {
            foreach ($jobs as $code => $job) {
                $job = $this->setJobData($job);
                $job['code'] = $code;
                $job['group'] = $group;
                $job['jobtype'] = 'xml';
                $data[$code] = $job;
            }
        }

        $totalRecords = count($data);

        return [
            'totalRecords' => $totalRecords,
            'items' => $data,
        ];
    }

    /**
     * Set the limit of the collection
     * @param int $offset
     * @param int $size
     */
    public function setLimit(
        $offset,
        $size
    ) {
        $this->size = $size;
        $this->offset = $offset;
    }

    /**
     * Set job data for given job
     * @param $job
     */
    private function setJobData($job)
    {
        if (!isset($job['config_schedule'])) {
            $job['config_schedule'] = '';
            if (isset($job['schedule'])) {
                $job['config_schedule'] = $job['schedule'];
            }
            if (isset($job['config_path'])) {
                $job['config_schedule'] = $this->helper->getGlobalConfig(
                    $job['config_path']
                );
            }
        }
        if (!isset($job['is_active'])) {
            $job['is_active'] = 1;
        }

        return $job;
    }
}

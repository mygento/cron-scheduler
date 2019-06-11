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
     * Cron job db xml text
     */
    const CRON_DB_XML = 'db_xml';
    /**
     * Cron job db text
     */
    const CRON_DB = 'db';
    /**
     * Cron job xml text
     */
    const CRON_XML = 'xml';

    /**
     * @var \Magento\Cron\Model\Config\Reader\Xml
     */
    private $reader;

    /**
     * @var \Magento\Cron\Model\Config\Reader\Db
     */
    private $dbReader;

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
        \Magento\Cron\Model\Config\Reader\Db $dbReader,
        \Magento\Cron\Model\Config\Reader\Xml $reader,
        $name,
        $primaryFieldName,
        $requestFieldName,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->cronConfig = $cronConfig;
        $this->helper = $helper;
        $this->dbReader = $dbReader;
        $this->reader = $reader;
    }

    public function getData()
    {
        $data = [];
        $configJobs = $this->cronConfig->getJobs();

        foreach ($configJobs as $group => $jobs) {
            foreach ($jobs as $code => $job) {
                $data[$code] = array_merge(
                    $this->setJobData($job),
                    [
                        'code' => $code,
                        'group' => $group,
                        'jobtype' => $this->getJobcodeType($code, $group),
                    ]
                );
            }
        }

        $totalRecords = count($data);

        return [
            'totalRecords' => $totalRecords,
            'items' => array_values($data),
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
            $job['config_schedule'] = $this->getCronExpression($job);
        }
        if (!isset($job['is_active'])) {
            $job['is_active'] = 1;
        }
        $job['executor'] = $job['instance'] . ':' . $job['method'];

        return $job;
    }

    /**
     * Get cron expression of cron job.
     *
     * @param array $jobConfig
     * @return string|null
     */
    private function getCronExpression($jobConfig)
    {
        $cronExpression = null;
        if (isset($jobConfig['config_path'])) {
            $cronExpression = $this->getConfigSchedule($jobConfig) ?: null;
        }

        if (!$cronExpression) {
            if (isset($jobConfig['schedule'])) {
                $cronExpression = $jobConfig['schedule'];
            }
        }

        return $cronExpression;
    }

    /**
     * Get config of schedule.
     *
     * @param array $jobConfig
     * @return string|null
     */
    private function getConfigSchedule($jobConfig)
    {
        return $this->helper->getGlobalConfig($jobConfig['config_path']);
    }

    /**
     * Get job code type(db, xml, db_xml)
     * @param $jobCode
     * @param $group
     * @return string
     */
    private function getJobcodeType($jobCode, $group)
    {
        $xmlJobs = $this->reader->read();
        $dbJobs = $this->dbReader->get();
        $xml = (isset($xmlJobs[$group][$jobCode])) ? true : false;
        $db = (isset($dbJobs[$group][$jobCode])) ? true : false;
        if ($xml && $db) {
            return self::CRON_DB_XML;
        }
        if (!$xml && $db) {
            return self::CRON_DB;
        }
        if ($xml && !$db) {
            $result = self::CRON_XML;
        }

        return $result;
    }
}

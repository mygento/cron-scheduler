<?php

/**
 * @author Mygento Team
 * @copyright 2019-2022 Mygento (https://www.mygento.ru)
 * @package Mygento_CronScheduler
 */

namespace Mygento\CronScheduler\Ui\DataProvider;

class Job extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    /**
     * Cron job db xml text
     */
    public const CRON_DB_XML = 'db_xml';

    /**
     * Cron job db text
     */
    public const CRON_DB = 'db';

    /**
     * Cron job xml text
     */
    public const CRON_XML = 'xml';

    /**
     * @var array
     */
    private $likeFilters = [];

    /**
     * @var array
     */
    private $rangeFilters = [];

    /**
     * @var string
     */
    private $sortField = 'code';

    /**
     * @var string
     */
    private $sortDir = 'asc';

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

    private $size = 20;
    private $offset = 1;

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

        //sorting
        $sortField = $this->sortField;
        $sortDir = $this->sortDir;
        usort($data, function ($a, $b) use ($sortField, $sortDir) {
            if ($sortDir == 'asc') {
                return $a[$sortField] > $b[$sortField];
            }

            return $a[$sortField] < $b[$sortField];
        });

        //filters
        foreach ($this->likeFilters as $column => $value) {
            $data = array_filter($data, function ($item) use ($column, $value) {
                return stripos($item[$column], $value) !== false;
            });
        }

        //pagination
        $result = array_slice($data, ($this->offset - 1) * $this->size, $this->size);

        $totalRecords = count($result);

        return [
            'totalRecords' => $totalRecords,
            'items' => array_values($result),
        ];
    }

    /**
     * Add filters to the collection
     * @param \Magento\Framework\Api\Filter $filter
     */
    public function addFilter(\Magento\Framework\Api\Filter $filter)
    {
        if ($filter->getConditionType() == 'like') {
            $this->likeFilters[$filter->getField()] = substr($filter->getValue(), 1, -1);
        } elseif ($filter->getConditionType() == 'eq') {
            $this->likeFilters[$filter->getField()] = $filter->getValue();
        } elseif ($filter->getConditionType() == 'gteq') {
            $this->rangeFilters[$filter->getField()]['from'] = $filter->getValue();
        } elseif ($filter->getConditionType() == 'lteq') {
            $this->rangeFilters[$filter->getField()]['to'] = $filter->getValue();
        }
    }

    /**
     * Set the order of the collection
     * @param string $field
     * @param string $direction
     */
    public function addOrder(
        $field,
        $direction
    ) {
        $this->sortField = $field;
        $this->sortDir = strtolower($direction);
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
        $job['executor'] = '';

        if (isset($job['instance']) && isset($job['method'])) {
            $job['executor'] = $job['instance'] . ':' . $job['method'];
        }

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

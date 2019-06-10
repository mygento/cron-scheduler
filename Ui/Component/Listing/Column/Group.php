<?php

/**
 * @author Mygento Team
 * @copyright 2019 Mygento (https://www.mygento.ru)
 * @package Mygento_CronScheduler
 */

namespace Mygento\CronScheduler\Ui\Component\Listing\Column;

class Group implements \Magento\Framework\Data\OptionSourceInterface
{
    /**
     * @var array
     */
    private $options;

    /**
     * @var \Magento\Cron\Model\ConfigInterface
     */
    private $cronConfig;

    /**
     * @param \Magento\Cron\Model\ConfigInterface $cronConfig
     */
    public function __construct(
        \Magento\Cron\Model\ConfigInterface $cronConfig
    ) {
        $this->cronConfig = $cronConfig;
    }

    /**
     * Get all options available
     * @return array
     */
    public function toOptionArray()
    {
        if ($this->options === null) {
            $configJobs = $this->cronConfig->getJobs();
            foreach (array_keys($configJobs) as $group) {
                $this->options[] = [
                    'label' => __($group),
                    'value' => $group,
                ];
            }
        }

        return $this->options;
    }
}

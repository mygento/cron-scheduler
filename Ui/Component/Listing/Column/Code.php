<?php

/**
 * @author Mygento Team
 * @copyright 2019-2022 Mygento (https://www.mygento.ru)
 * @package Mygento_CronScheduler
 */

namespace Mygento\CronScheduler\Ui\Component\Listing\Column;

class Code implements \Magento\Framework\Data\OptionSourceInterface
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
        $options = [];
        if ($this->options === null) {
            $configJobs = $this->cronConfig->getJobs();
            foreach (array_values($configJobs) as $jobs) {
                foreach (array_keys($jobs) as $code) {
                    $options[] = $code;
                }
            }
        }
        sort($options);
        foreach ($options as $option) {
            $this->options[] = [
                'label' => $option, 'value' => $option,
            ];
        }

        return $this->options;
    }
}

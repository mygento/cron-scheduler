<?php

/**
 * @author Mygento Team
 * @copyright 2019 Mygento (https://www.mygento.ru)
 * @package Mygento_CronScheduler
 */

namespace Mygento\CronScheduler\Ui\Component\Listing\Column;

class Form implements \Magento\Framework\Data\OptionSourceInterface
{
    /**
     * @var array
     */
    private $options;

    /**
     * Get all options available
     * @return array
     */
    public function toOptionArray()
    {
        if ($this->options === null) {
            $this->options = [
                ['label' => __('Enable'),  'value' => 1],
                ['label' => __('Disable'), 'value' => 0],
            ];
        }

        return $this->options;
    }
}

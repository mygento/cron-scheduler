<?php

/**
 * @author Mygento Team
 * @copyright 2017-2019 Mygento (https://www.mygento.ru)
 * @package Mygento_Configsync
 */

namespace Mygento\CronScheduler\Console\Command;

use Mygento\CronScheduler\Api\ScheduleManagementInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class DropRunningJobs
 * @package Mygento\CronScheduler\Console\Command
 */
class DropRunningJobs extends Command
{
    /**
     * @var OutputInterface
     */
    private $output;

    /**
     * @var ScheduleManagementInterface
     */
    private $scheduleManagement;

    /**
     * DropRunningJobs constructor.
     * @param ScheduleManagementInterface $scheduleManagement
     * @param string|null $name
     */
    public function __construct(
        ScheduleManagementInterface $scheduleManagement,
        string $name = null
    ) {
        $this->scheduleManagement = $scheduleManagement;

        parent::__construct($name);
    }

    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this
            ->setName('mygento:cron:drop_running')
            ->setDescription('Drop running jobs if running jobs timeout exceeded.');

        parent::configure();
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->output = $output;
        $this->output->writeln("<info>Start dropping of running jobs.</info>");

        try {
            $this->scheduleManagement->dropExceededRunningJobs();
        } catch (\Exception $e) {
            $this->output->writeln("<error>{$e->getMessage()}</error>");
        }

        $this->output->writeln('<info>Done</info>');

        return 0;
    }
}

<?php

/**
 * @author Mygento Team
 * @copyright 2019-2022 Mygento (https://www.mygento.ru)
 * @package Mygento_CronScheduler
 */

namespace Mygento\CronScheduler\Controller\Adminhtml\Schedule;

use Magento\Framework\Controller\ResultFactory;

class Timeline extends \Magento\Backend\App\Action
{
    private $helper;

    public function __construct(
        \Mygento\CronScheduler\Helper\Cron $helper,
        \Magento\Backend\App\Action\Context $context
    ) {
        parent::__construct($context);

        $this->helper = $helper;
    }

    /**
     * Action to display the tasks timeline
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $this->helper->getCronStatus();
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->setActiveMenu('Magento_Backend::system');
        $resultPage->getConfig()->getTitle()->prepend(__('Cron Schedule Timeline'));
        $resultPage->addBreadcrumb(__('Cron Scheduler'), __('Cron Scheduler'));

        return $resultPage;
    }
}

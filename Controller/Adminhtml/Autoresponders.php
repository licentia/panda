<?php
/**
 * Copyright (C) Licentia, Unipessoal LDA
 *
 * NOTICE OF LICENSE
 *  
 *  This source file is subject to the EULA
 *  that is bundled with this package in the file LICENSE.txt.
 *  It is also available through the world-wide-web at this URL:
 *  https://www.greenflyingpanda.com/panda-license.txt
 *  
 *  @title      Licentia Panda - Magento® Sales Automation Extension
 *  @package    Licentia
 *  @author     Bento Vilas Boas <bento@licentia.pt>
 *  @copyright  Copyright (c) Licentia - https://licentia.pt
 *  @license    https://www.greenflyingpanda.com/panda-license.txt
 *
 */

namespace Licentia\Panda\Controller\Adminhtml;

/**
 * Autoresponders controller
 */
class Autoresponders extends \Magento\Backend\App\Action
{

    /**
     * Authorization level of a basic admin session
     */
    const ADMIN_RESOURCE = 'Licentia_Panda::autoresponders';

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $registry = null;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var \Magento\Backend\Model\View\Result\ForwardFactory
     */
    protected $resultForwardFactory;

    /**
     * @var \Magento\Framework\View\Result\LayoutFactory
     */
    protected $layoutFactory;

    /**
     * @var \Licentia\Panda\Model\SubscribersFactory
     */
    protected $autorespondersFactory;

    /**
     * @var \Licentia\Panda\Helper\Data
     */
    protected $pandaHelper;

    /**
     * @param \Magento\Backend\App\Action\Context               $context
     * @param \Magento\Framework\View\Result\PageFactory        $resultPageFactory
     * @param \Magento\Framework\Registry                       $registry
     * @param \Licentia\Panda\Helper\Data                       $pandaHelper
     * @param \Licentia\Panda\Model\AutorespondersFactory       $autorespondersFactory
     * @param \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory
     * @param \Magento\Framework\View\Result\LayoutFactory      $resultLayoutFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Registry $registry,
        \Licentia\Panda\Helper\Data $pandaHelper,
        \Licentia\Panda\Model\AutorespondersFactory $autorespondersFactory,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory,
        \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory
    ) {

        parent::__construct($context);

        /*
        $i = 0;

        if ($i) {
            ini_set('display_errors', 1);
            error_reporting(-1);


            \Magento\Framework\App\ObjectManager::getInstance()->create('\Licentia\Reports\Cron\RebuildSalesStatsForYesterday')->execute();
            #\Magento\Framework\App\ObjectManager::getInstance()->get('Licentia\Panda\Model\Splits')->cron();

            #\Magento\Framework\App\ObjectManager::getInstance()->get('Licentia\Panda\Model\Products\Relations')->rebuildAvgDays();
            #$order = \Magento\Framework\App\ObjectManager::getInstance()->create('Magento\Sales\Model\Order')->load(55);
            #\Magento\Framework\App\ObjectManager::getInstance()->get('Licentia\Panda\Model\Autoresponders')->newOrder($order);
            #\Magento\Framework\App\ObjectManager::getInstance()->get('Licentia\Panda\Model\Autoresponders')->cron();
            #\Magento\Framework\App\ObjectManager::getInstance()->get('Licentia\Panda\Model\Followup')->cron();
            #\Magento\Framework\App\ObjectManager::getInstance()->get('Licentia\Panda\Model\Campaigns')->queueCampaigns();
            #\Magento\Framework\App\ObjectManager::getInstance()->get('Licentia\Panda\Model\Service\Smtp')->sendEmail();
            #\Magento\Framework\App\ObjectManager::getInstance()->get('Licentia\Panda\Model\Service\Sms')->sendSms();
            #\Magento\Framework\App\ObjectManager::getInstance()->get('Licentia\Panda\Model\Metadata')->activityRelated();
            #\Magento\Framework\App\ObjectManager::getInstance()->get('Licentia\Panda\Model\Bounces')->processBounces();
            #\Magento\Framework\App\ObjectManager::getInstance()->get('Licentia\Panda\Model\Stats')->randomStats();
            #\Magento\Framework\App\ObjectManager::getInstance()->get('Licentia\Panda\Model\Followup')->getSubscribersObject();
            #\Magento\Framework\App\ObjectManager::getInstance()->get('Licentia\Panda\Model\Metadata')->rebuildCustomerMetadata();

            die(__METHOD__);
        }

        */
        $this->resultForwardFactory = $resultForwardFactory;
        $this->resultPageFactory = $resultPageFactory;
        $this->registry = $registry;
        $this->layoutFactory = $resultLayoutFactory;
        $this->autorespondersFactory = $autorespondersFactory;
        $this->pandaHelper = $pandaHelper;
    }

    /**
     *
     */
    public function execute()
    {

        $model = $this->autorespondersFactory->create();
        $id = $this->getRequest()->getParam('id');
        if ($id) {
            $model->load($id);
        }

        if ($data = $this->_getSession()->getFormData(true)) {
            $model->addData($data);
        }

        if ($id) {
            $model->setId($id);
        }

        $this->registry->register('panda_autoresponder', $model, true);
    }

}

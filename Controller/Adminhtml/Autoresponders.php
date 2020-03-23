<?php
/**
 * Copyright (C) 2020 Licentia, Unipessoal LDA
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 *
 * @title      Licentia Panda - Magento® Sales Automation Extension
 * @package    Licentia
 * @author     Bento Vilas Boas <bento@licentia.pt>
 * @copyright  Copyright (c) Licentia - https://licentia.pt
 * @license    GNU General Public License V3
 * @modified   23/03/20, 02:28 GMT
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

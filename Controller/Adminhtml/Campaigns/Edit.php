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
 * @modified   29/01/20, 15:22 GMT
 *
 */

namespace Licentia\Panda\Controller\Adminhtml\Campaigns;

use Magento\Backend\App\Action;

/**
 * Class Edit
 *
 * @package Licentia\Panda\Controller\Adminhtml\Campaigns
 */
class Edit extends \Licentia\Panda\Controller\Adminhtml\Campaigns
{

    /**
     * @var \Licentia\Panda\Model\ResourceModel\Followup\CollectionFactory
     */
    protected $followupCollection;

    /**
     * @var \Licentia\Panda\Helper\Data
     */
    protected $pandaHelper;

    /**
     *
     * @param \Magento\Backend\App\Action\Context                                                            $context
     * @param \Magento\Framework\View\Result\PageFactory                                                     $resultPageFactory
     * @param \Licentia\Panda\Model\SendersFactory                                                           $sendersFactory
     * @param \Magento\Framework\Registry                                                                    $registry
     * @param \Licentia\Panda\Model\CampaignsFactory                                                         $campaignsFactory
     * @param \Magento\Backend\Model\View\Result\ForwardFactory|\Magento\Framework\Controller\Result\Forward $resultForwardFactory
     * @param \Magento\Framework\App\Response\Http\FileFactory                                               $fileFactory
     * @param \Licentia\Panda\Helper\Data                                                                    $pandaHelper
     * @param \Licentia\Panda\Model\ResourceModel\Followup\CollectionFactory                                 $followupCollection
     * @param \Magento\Framework\View\Result\LayoutFactory                                                   $resultLayoutFactory
     */
    public function __construct(
        Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Licentia\Panda\Model\SendersFactory $sendersFactory,
        \Magento\Framework\Registry $registry,
        \Licentia\Panda\Model\CampaignsFactory $campaignsFactory,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        \Licentia\Panda\Helper\Data $pandaHelper,
        \Licentia\Panda\Model\ResourceModel\Followup\CollectionFactory $followupCollection,
        \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory
    ) {

        parent::__construct(
            $context,
            $resultPageFactory,
            $sendersFactory,
            $registry,
            $campaignsFactory,
            $resultForwardFactory,
            $fileFactory,
            $resultLayoutFactory
        );

        $this->followupCollection = $followupCollection;
        $this->pandaHelper = $pandaHelper;
    }

    /**
     * Init actions
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    protected function _initAction()
    {

        // load layout, set active menu and breadcrumbs
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Licentia_Panda::campaigns')
                   ->addBreadcrumb(__('Sales Automation'), __('Sales Automation'))
                   ->addBreadcrumb(__('Manage Campaigns'), __('Manage Campaigns'));

        return $resultPage;
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {

        parent::execute();
        $id = $this->getRequest()->getParam('id');

        /** @var \Licentia\Panda\Model\Campaigns $model */
        $model = $this->registry->registry('panda_campaign');

        if ($id) {
            if (!$model->getId()) {
                $this->messageManager->addErrorMessage(__('This Campaign no longer exists.'));
                $resultRedirect = $this->resultRedirectFactory->create();

                return $resultRedirect->setPath('*/*/');
            }
        }

        if ($model->getStatus() == 'finished') {
            $this->messageManager->addNoticeMessage(
                __(
                    "This campaign is now closed. You can't modify it. Click on 'Duplicate & Save' to duplicate and edit it."
                )
            );
        }

        if (!$model->getSegmentsIds()) {
            $model->setSegmentsIds('0');
        }
        if (!is_array($model->getSegmentsIds())) {
            $model->setData("segments_ids", explode(',', $model->getSegmentsIds()));
        }
        if (!is_array($model->getRecurringDaily())) {
            $model->setRecurringDaily(explode(',', $model->getRecurringDaily()));
        }

        if (!$model->getStoreId()) {
            $model->setStoreId('0');
        }
        if (!is_array($model->getStoreId())) {
            $model->setStoreId(explode(',', $model->getStoreId()));
        }

        $followup = $this->followupCollection->create()
                                             ->addFieldToFilter('campaign_id', $model->getId());
        $this->registry->register('panda_followup_collection', $followup);

        $data = $this->_getSession()->getFormData(true);
        if (!empty($data)) {
            $model->addData($data);
        }

        $resultPage = $this->_initAction();
        $resultPage->addBreadcrumb(
            $id ? __('Edit Campaign') : __('New Campaign'),
            $id ? __('Edit Campaign') : __('New Campaign')
        );
        $resultPage->getConfig()
                   ->getTitle()->prepend(__('Campaigns'));
        $resultPage->getConfig()
                   ->getTitle()->prepend($model->getId() ? $model->getInternalName() : __('New Campaign'));

        $resultPage->addContent(
            $resultPage->getLayout()
                       ->createBlock('Licentia\Panda\Block\Adminhtml\Campaigns\Edit')
        )
                   ->addLeft(
                       $resultPage->getLayout()
                                  ->createBlock('Licentia\Panda\Block\Adminhtml\Campaigns\Edit\Tabs')
                   );

        return $resultPage;
    }
}

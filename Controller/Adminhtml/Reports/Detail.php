<?php
/*
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

namespace Licentia\Panda\Controller\Adminhtml\Reports;

/**
 * Class Detail
 *
 * @package Licentia\Panda\Controller\Adminhtml\Reports
 */
class Detail extends \Licentia\Panda\Controller\Adminhtml\Reports
{

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
        $resultPage->setActiveMenu('Licentia_Panda::reports')
                   ->addBreadcrumb(__('Sales Automation'), __('Sales Automation'))
                   ->addBreadcrumb(__('Reports'), __('Reports'));

        return $resultPage;
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {

        parent::execute();

        $id = $this->getRequest()->getParam('id');
        $this->getRequest()->setParams(['campaign_id' => $id]);

        $camp = $this->campaignsFactory->create()->load($id);

        if (!$camp->getId()) {
            $this->messageManager->addErrorMessage(__('Campaign does not exist'));

            return $this->resultRedirectFactory->create()->setUrl($this->_redirect->getRefererUrl());
        }
        if ($camp->getSent() == 0) {
            $this->messageManager->addErrorMessage(__('No messages have been sent yet from this campaign.'));

            return $this->resultRedirectFactory->create()->setUrl($this->_redirect->getRefererUrl());
        }

        if ($camp->getRecurring() != '0') {
            return $this->resultRedirectFactory->create()
                                               ->setPath(
                                                   '*/campaigns/edit',
                                                   ['id' => $camp->getId(), 'tab_id' => 'reports_edit_tabs_children']
                                               );
        }

        $stats = $this->statsFactory->create()->loadCampaign();

        $this->registry->register('panda_report_campaign', $camp);
        $this->registry->register('panda_campaign', $camp);
        $this->registry->register('panda_stats', $stats);
        $resultPage = $this->_initAction();
        $resultPage->addContent(
            $resultPage->getLayout()
                       ->createBlock('Licentia\Panda\Block\Adminhtml\Reports\Edit')
        )
                   ->addLeft(
                       $resultPage->getLayout()
                                  ->createBlock('Licentia\Panda\Block\Adminhtml\Reports\Edit\Tabs')
                   );
        /** @var \Licentia\Panda\Model\Campaigns $campaign */
        if ($campaign = $this->registry->registry('panda_campaign')) {
            $resultPage->getConfig()
                       ->getTitle()->set(__('Campaign Report for %1', $campaign->getInternalName()));
        }

        return $resultPage;
    }
}

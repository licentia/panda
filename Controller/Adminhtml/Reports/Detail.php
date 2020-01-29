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

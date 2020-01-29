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

namespace Licentia\Panda\Controller\Adminhtml\Splits;

/**
 * Class Edit
 *
 * @package Licentia\Panda\Controller\Adminhtml\Splits
 */
class Edit extends \Licentia\Panda\Controller\Adminhtml\Splits
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
        $resultPage->setActiveMenu('Licentia_Panda::splits')
                   ->addBreadcrumb(__('Sales Automation'), __('Sales Automation'))
                   ->addBreadcrumb(__('Manage A/B Campaigns'), __('Manage A/B Campaigns'));

        return $resultPage;
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Page|\Magento\Backend\Model\View\Result\Redirect
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function execute()
    {

        parent::execute();
        $id = $this->getRequest()->getParam('id');

        /** @var \Licentia\Panda\Model\Splits $model */
        $model = $this->registry->registry('panda_split');

        if ($id) {
            if (!$model->getId()) {
                $this->messageManager->addErrorMessage(__('This A/B Campaign no longer exists.'));
                $resultRedirect = $this->resultRedirectFactory->create();

                return $resultRedirect->setPath('*/*/');
            }
        }

        $data = $this->_getSession()->getFormData(true);
        if (!empty($data)) {
            $model->setData($data);
        }

        if ($model->getId() && $model->getSent() == 1 && $model->getClosed() == 0) {
            $this->messageManager->addNoticeMessage(__("This A/B Campaign is running..."));
        } elseif ($model->getId() && $model->getClosed() == 1) {
            $this->messageManager->addNoticeMessage(__("This A/B Campaign is closed..."));
        }

        if (!$model->getSegmentsIds()) {
            $model->setSegmentsIds('0');
        }
        if (!is_array($model->getSegmentsIds())) {
            $model->setSegmentsIds(explode(',', $model->getSegmentsIds()));
        }

        if (!$model->getStoreId()) {
            $model->setStoreId('0');
        }
        if (!is_array($model->getStoreId())) {
            $model->setStoreId(explode(',', $model->getStoreId()));
        }

        $resultPage = $this->_initAction();
        $resultPage->addBreadcrumb(
            $id ? __('Edit A/B Campaign') : __('New A/B Campaign'),
            $id ? __('Edit A/B Campaign') : __('New A/B Campaign')
        );
        $resultPage->getConfig()
                   ->getTitle()->prepend(__('A/B Campaigns'));
        $resultPage->getConfig()
                   ->getTitle()->prepend($model->getId() ? $model->getName() : __('New A/B Campaign'));

        $resultPage->addContent(
            $resultPage->getLayout()
                       ->createBlock('Licentia\Panda\Block\Adminhtml\Splits\Edit')
        )
                   ->addLeft(
                       $resultPage->getLayout()
                                  ->createBlock('Licentia\Panda\Block\Adminhtml\Splits\Edit\Tabs')
                   );

        return $resultPage;
    }
}

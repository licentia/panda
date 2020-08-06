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

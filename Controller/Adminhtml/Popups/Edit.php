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

namespace Licentia\Panda\Controller\Adminhtml\Popups;

/**
 * Class Edit
 *
 * @package Licentia\Panda\Controller\Adminhtml\Popups
 */
class Edit extends \Licentia\Panda\Controller\Adminhtml\Popups
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
        $resultPage->setActiveMenu('Licentia_Panda::popups')
                   ->addBreadcrumb(__('Floating Windows'), __('Floating Windows'))
                   ->addBreadcrumb(__('Manage Floating Windows'), __('Manage Floating Windows'));

        return $resultPage;
    }

    /**
     * @return $this|\Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {

        parent::execute();
        $id = $this->getRequest()->getParam('id');

        /** @var \Licentia\Panda\Model\Popups $model */
        $model = $this->registry->registry('panda_popup');

        if ($id) {
            if (!$model->getId()) {
                $this->messageManager->addErrorMessage(__('This %1 no longer exists.', $model->getTypeName()));
                /** \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
                $resultRedirect = $this->resultRedirectFactory->create();

                return $resultRedirect->setPath('*/*/');
            }
        }

        $data = $this->_getSession()->getFormData(true);
        if (!empty($data)) {
            $model->setData($data);
        }

        if (!$model->getBlocksIds()) {
            $model->setBlocksIds('0');
        }
        $model->setBlocksIds(explode(',', $model->getBlocksIds()));

        if (!$model->getSegmentsIds()) {
            $model->setSegmentsIds('0');
        }
        $model->setSegmentsIds(explode(',', $model->getSegmentsIds()));

        if (!$model->getStoreId()) {
            $model->setStoreId('0');
        }
        $model->setStoreId(explode(',', $model->getStoreId()));

        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->_initAction();
        $resultPage->addBreadcrumb(
            $id ? __('Edit %1', $model->getTypeName()) : __('New %1', $model->getTypeName()),
            $id ? __('Edit %1', $model->getTypeName()) : __('New %1', $model->getTypeName())
        );
        $resultPage->getConfig()
                   ->getTitle()->prepend(__('Floating Windows'));
        $resultPage->getConfig()
                   ->getTitle()->prepend($model->getId() ? $model->getName() : __('New %1', $model->getTypeName()));

        $resultPage->addContent(
            $resultPage->getLayout()
                       ->createBlock('Licentia\Panda\Block\Adminhtml\Popups\Edit')
        )
                   ->addLeft(
                       $resultPage->getLayout()
                                  ->createBlock('Licentia\Panda\Block\Adminhtml\Popups\Edit\Tabs')
                   );

        return $resultPage;
    }
}

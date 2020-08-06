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

namespace Licentia\Panda\Controller\Adminhtml\Templates;

/**
 * Class Edit
 *
 * @package Licentia\Panda\Controller\Adminhtml\Templates
 */
class Edit extends \Licentia\Panda\Controller\Adminhtml\Templates
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
        $resultPage->setActiveMenu('Licentia_Panda::templates')
                   ->addBreadcrumb(__('Message Templates'), __('Message Templates'))
                   ->addBreadcrumb(__('Manage Message Templates'), __('Manage Message Templates'));

        return $resultPage;
    }

    /**
     * @return $this|\Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {

        parent::execute();
        $id = $this->getRequest()->getParam('id');
        /** @var \Licentia\Panda\Model\Templates $model */
        $model = $this->registry->registry('panda_template');

        if ($id) {
            if (!$model->getId()) {
                $this->messageManager->addErrorMessage(__('This Message Template no longer exists.'));
                /** \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
                $resultRedirect = $this->resultRedirectFactory->create();

                return $resultRedirect->setPath('*/*/');
            }
        }

        $data = $this->_getSession()->getFormData(true);
        if (!empty($data)) {
            $model->setData($data);
        }

        if ($model->getParentId()) {

            if (!$model->getSegmentsIds()) {
                $model->setSegmentsIds('0');
            }
            $model->setSegmentsIds(explode(',', $model->getSegmentsIds()));

            if (!$model->getStoreId()) {
                $model->setStoreId('0');
            }
            $model->setStoreId(explode(',', $model->getStoreId()));

            if (!$model->getAge()) {
                $model->setAge('0');
            }
            $model->setAge(explode(',', $model->getAge()));
        }

        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->_initAction();
        $resultPage->addBreadcrumb(
            $id ? __('Edit Message Template') : __('New Message Template'),
            $id ? __('Edit Message Template') : __('New Message Template')
        );
        $resultPage->getConfig()
                   ->getTitle()->prepend(__('Message Templates'));
        $resultPage->getConfig()
                   ->getTitle()->prepend($model->getId() ? $model->getName() : __('New Message Template'));

        $resultPage->addContent(
            $resultPage->getLayout()
                       ->createBlock('Licentia\Panda\Block\Adminhtml\Templates\Edit')
        )
                   ->addLeft(
                       $resultPage->getLayout()
                                  ->createBlock('Licentia\Panda\Block\Adminhtml\Templates\Edit\Tabs')
                   );

        return $resultPage;
    }
}

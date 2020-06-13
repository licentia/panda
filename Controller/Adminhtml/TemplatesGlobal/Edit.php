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

namespace Licentia\Panda\Controller\Adminhtml\TemplatesGlobal;

/**
 * Class Edit
 *
 * @package Licentia\Panda\Controller\Adminhtml\TemplatesGlobal
 */
class Edit extends \Licentia\Panda\Controller\Adminhtml\TemplatesGlobal
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
                   ->addBreadcrumb(__('TemplatesGlobal'), __('TemplatesGlobal'))
                   ->addBreadcrumb(__('Manage TemplatesGlobal'), __('Manage TemplatesGlobal'));

        return $resultPage;
    }

    /**
     * @return $this|\Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {

        parent::execute();
        $id = $this->getRequest()->getParam('id');

        /** @var \Licentia\Panda\Model\TemplatesGlobal $model */
        $model = $this->registry->registry('panda_template_global');

        if ($id) {
            if (!$model->getId()) {
                $this->messageManager->addErrorMessage(__('This Template no longer exists.'));
                /** \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
                $resultRedirect = $this->resultRedirectFactory->create();

                return $resultRedirect->setPath('*/*/');
            }
        }

        $data = $this->_getSession()->getFormData(true);
        if (!empty($data)) {
            $model->setData($data);
        }

        if (!$model->getStoreId()) {
            $model->setStoreId('0');
        }
        $model->setStoreId(explode(',', $model->getStoreId()));

        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->_initAction();
        $resultPage->addBreadcrumb(
            $id ? __('Edit Design Template') : __('New Design Template'),
            $id ? __('Edit Design Template') : __('New Design Template')
        );
        $resultPage->getConfig()
                   ->getTitle()->prepend(__('Design Templates'));
        $resultPage->getConfig()
                   ->getTitle()->prepend($model->getId() ? $model->getName() : __('New Design Template'));

        $resultPage->addContent(
            $resultPage->getLayout()
                       ->createBlock('Licentia\Panda\Block\Adminhtml\TemplatesGlobal\Edit')
        )
                   ->addLeft(
                       $resultPage->getLayout()
                                  ->createBlock('Licentia\Panda\Block\Adminhtml\TemplatesGlobal\Edit\Tabs')
                   );

        return $resultPage;
    }
}

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
 * @modified   29/03/20, 02:49 GMT
 *
 */

namespace Licentia\Panda\Controller\Adminhtml\Subscriber;

/**
 * Class Edit
 *
 * @package Licentia\Panda\Controller\Adminhtml\Subscriber
 */
class Edit extends \Licentia\Panda\Controller\Adminhtml\Subscriber
{

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * Edit constructor.
     *
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfigInterface
     * @param \Magento\Backend\App\Action\Context                $context
     * @param \Magento\Framework\View\Result\PageFactory         $resultPageFactory
     * @param \Magento\Framework\Registry                        $registry
     * @param \Licentia\Panda\Model\ExtraFieldsFactory           $extraFieldsFactory
     * @param \Licentia\Panda\Model\SubscribersFactory           $subscribersFactory
     * @param \Magento\Backend\Model\View\Result\ForwardFactory  $resultForwardFactory
     * @param \Magento\Framework\App\Response\Http\FileFactory   $fileFactory
     * @param \Magento\Framework\View\Result\LayoutFactory       $resultLayoutFactory
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfigInterface,
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Registry $registry,
        \Licentia\Panda\Model\ExtraFieldsFactory $extraFieldsFactory,
        \Licentia\Panda\Model\SubscribersFactory $subscribersFactory,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory
    ) {

        parent::__construct(
            $context,
            $resultPageFactory,
            $registry,
            $extraFieldsFactory,
            $subscribersFactory,
            $resultForwardFactory,
            $fileFactory,
            $resultLayoutFactory
        );

        $this->scopeConfig = $scopeConfigInterface;
    }

    /**
     * Init actions
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    protected function _initAction()
    {

        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Licentia_Panda::subscribers')
                   ->addBreadcrumb(__('Subscribers'), __('Subscribers'))
                   ->addBreadcrumb(__('Manage Subscribers'), __('Manage Subscribers'));

        return $resultPage;
    }

    /**
     * @return $this|\Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {

        parent::execute();
        $id = $this->getRequest()->getParam('id');

        /** @var \Licentia\Panda\Model\Subscribers $model */
        $model = $this->registry->registry('panda_subscriber');

        if (!$this->scopeConfig->isSetFlag('panda_nuntius/info/enabled')) {
            $this->messageManager->addErrorMessage(__('Subscriber Management has been disabled in Configuration'));

            /** \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
            $resultRedirect = $this->resultRedirectFactory->create();

            return $resultRedirect->setPath('*/*/');
        }

        if ($id) {
            if (!$model->getId()) {
                $this->messageManager->addErrorMessage(__('This subscriber no longer exists.'));
                /** \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
                $resultRedirect = $this->resultRedirectFactory->create();

                return $resultRedirect->setPath('*/*/');
            }
        }

        $data = $this->_getSession()->getFormData(true);
        if (!empty($data)) {
            $model->setData($data);
        }

        $extraFields = $this->extraFieldsFactory->create()
                                                ->getCollection()->addFieldToFilter('type', 'options');

        /** @var \Licentia\Panda\Model\ExtraFields $extraField */
        foreach ($extraFields as $extraField) {
            $options = str_getcsv($model->getData('field_' . $extraField->getEntryCode()));
            $options = array_filter($options);
            $options = array_map('trim', $options);
            $model->setData('field_' . $extraField->getEntryCode(), $options);
        }

        $model->setData('segments', $model->getSubscriberSegmentsAsArray());

        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->_initAction();
        $resultPage->addBreadcrumb(
            $id ? __('Edit Subscriber') : __('New Subscriber'),
            $id ? __('Edit Subscriber') : __('New Subscriber')
        );
        $resultPage->getConfig()
                   ->getTitle()->prepend(__('Subscribers'));
        $resultPage->getConfig()
                   ->getTitle()->prepend($model->getId() ? $model->getName() : __('New Subscriber'));

        $resultPage->addContent(
            $resultPage->getLayout()
                       ->createBlock('Licentia\Panda\Block\Adminhtml\Subscriber\Edit')
        )
                   ->addLeft(
                       $resultPage->getLayout()
                                  ->createBlock('Licentia\Panda\Block\Adminhtml\Subscriber\Edit\Tabs')
                   );

        return $resultPage;
    }
}

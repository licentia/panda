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

namespace Licentia\Panda\Controller\Adminhtml\Autoresponders;

use Magento\Backend\App\Action;

/**
 * Class Edit
 *
 * @package Licentia\Panda\Controller\Adminhtml\Autoresponders
 */
class Edit extends \Licentia\Panda\Controller\Adminhtml\Autoresponders
{

    /**
     * @var \Licentia\Panda\Model\ResourceModel\Chainsedit\CollectionFactory
     */
    protected $chainseditCollection;

    /**
     * @var \Licentia\Panda\Model\ResourceModel\Chains\CollectionFactory
     */
    protected $chainsCollection;

    /**
     * @var \Licentia\Panda\Model\ChainsFactory
     */
    protected $chainsFactory;

    /**
     * @var \Licentia\Panda\Model\ResourceModel\Chains
     */
    protected $chainsResource;

    /**
     * Edit constructor.
     *
     * @param Action\Context                                                   $context
     * @param \Magento\Framework\View\Result\PageFactory                       $resultPageFactory
     * @param \Magento\Framework\Registry                                      $registry
     * @param \Licentia\Panda\Model\ChainsFactory                              $chainsFactory
     * @param \Licentia\Panda\Model\ResourceModel\Chains                       $chainsResource
     * @param \Licentia\Panda\Model\ResourceModel\Chains\CollectionFactory     $chainsCollection
     * @param \Licentia\Panda\Model\ResourceModel\Chainsedit\CollectionFactory $chainseditCollection
     * @param \Licentia\Panda\Helper\Data                                      $pandaHelper
     * @param \Licentia\Panda\Model\AutorespondersFactory                      $autorespondersFactory
     * @param \Magento\Backend\Model\View\Result\ForwardFactory                $resultForwardFactory
     * @param \Magento\Framework\View\Result\LayoutFactory                     $resultLayoutFactory
     */
    public function __construct(
        Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Registry $registry,
        \Licentia\Panda\Model\ChainsFactory $chainsFactory,
        \Licentia\Panda\Model\ResourceModel\Chains $chainsResource,
        \Licentia\Panda\Model\ResourceModel\Chains\CollectionFactory $chainsCollection,
        \Licentia\Panda\Model\ResourceModel\Chainsedit\CollectionFactory $chainseditCollection,
        \Licentia\Panda\Helper\Data $pandaHelper,
        \Licentia\Panda\Model\AutorespondersFactory $autorespondersFactory,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory,
        \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory
    ) {

        $this->chainsFactory = $chainsFactory;
        $this->chainsResource = $chainsResource;
        $this->chainsCollection = $chainsCollection;
        $this->chainseditCollection = $chainseditCollection;

        parent::__construct(
            $context,
            $resultPageFactory,
            $registry,
            $pandaHelper,
            $autorespondersFactory,
            $resultForwardFactory,
            $resultLayoutFactory
        );
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
        $resultPage->setActiveMenu('Licentia_Panda::autoresponders')
                   ->addBreadcrumb(__('Sales Automation'), __('Sales Automation'))
                   ->addBreadcrumb(__('Manage Autoresponders'), __('Manage Autoresponders'));

        return $resultPage;
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Page
     * @throws \Exception
     */
    public function execute()
    {

        parent::execute();
        $id = $this->getRequest()->getParam('id');

        /** @var \Licentia\Panda\Model\Autoresponders $model */
        $model = $this->registry->registry('panda_autoresponder');

        if ($id) {
            if (!$model->getId()) {
                $this->messageManager->addErrorMessage(__('This Autoresponder no longer exists.'));
                $resultRedirect = $this->resultRedirectFactory->create();

                return $resultRedirect->setPath('*/*/');
            }
        }

        $data = $this->_getSession()->getFormData(true);
        if (!empty($data)) {
            $model->setData($data);
        }

        if (!$model->getId()) {
            $this->messageManager->addNoticeMessage(
                'Please save the Autoresponder to assign actions and change other information'
            );
        }

        if ($model->getUtm()) {
            $utm = json_decode($model->getUtm(), true);
            $model->setData('utm_parameter', $utm['utm_parameter']);
            $model->setData('utm_condition', $utm['utm_condition']);
            $model->setData('utm_match', $utm['utm_match']);
        }

        if ($model->getId()) {
            $current = $this->chainsCollection->create()->addFieldToFilter('autoresponder_id', $model->getId());

            $old = $this->chainseditCollection->create()->addFieldToFilter('autoresponder_id', $model->getId());

            foreach ($old as $item) {
                $item->delete();
            }

            $delete = true;
            foreach ($current as $item) {
                if ($item->getData('parent_id') == 0) {
                    $delete = false;
                }
            }

            if ($current->count() == 0 || $delete) {
                foreach ($current as $item) {
                    $item->delete();
                }

                $data = [
                    'autoresponder_id' => $model->getId(),
                    'parent_id'        => 0,
                    'editable'         => 0,
                    'name'             => 'Triggers Fired',
                ];

                $this->chainsFactory->create()
                                    ->setData($data)
                                    ->save();

                $current = $this->chainsCollection->create()->addFieldToFilter('autoresponder_id', $model->getId());
            }

            foreach ($current as $item) {
                $data = $item->getData();
                $this->chainsResource->getConnection()
                                     ->insert(
                                         $this->chainsResource->getTable('panda_autoresponders_chains_edit'),
                                         $data
                                     );
            }
        }

        if (!$model->getSegmentsIds()) {
            $model->setSegmentsIds('0');
        }
        $model->setSegmentsIds(explode(',', $model->getSegmentsIds()));

        if (!$model->getStoreId()) {
            $model->setStoreId('0');
        }
        $model->setStoreId(explode(',', $model->getStoreId()));

        if (!$model->getNewCustomerGroupId()) {
            $model->setNewCustomerGroupId('0');
        }
        $model->setNewCustomerGroupId(explode(',', $model->getNewCustomerGroupId()));

        if (!$model->getOldCustomerGroupId()) {
            $model->setOldCustomerGroupId('0');
        }
        $model->setOldCustomerGroupId(explode(',', $model->getOldCustomerGroupId()));

        if (!is_array($model->getPaymentMethod())) {
            $model->setPaymentMethod(explode(',', $model->getPaymentMethod()));
        }
        if (!is_array($model->getShippingMethod())) {
            $model->setShippingMethod(explode(',', $model->getShippingMethod()));
        }

        if ($model->getSkipDays()) {
            $model->setSkipDays(explode(',', $model->getSkipDays()));
        }

        if ($model->getProducts()) {
            $model->setProducts(str_replace(',', "\n", $model->getProducts()));
        }

        if ($model->getProduct()) {
            $model->setProduct(str_replace(',', "\n", $model->getProduct()));
        }
        if (!is_array($model->getCategories())) {
            if ($model->getCategories()) {
                $model->setCategories(explode(',', $model->getCategories()));
            }
        }

        $names = $model->getAutorespondersTriggersDetails();

        $eventName = $this->getRequest()->getParam('event', $model->getEvent());

        $resultPage = $this->_initAction();
        $resultPage->addBreadcrumb(
            $id ? __('Edit Autoresponder') : __('New Autoresponder - %1', $names[$eventName]),
            $id ? __('Edit Autoresponder') : __('New Autoresponder - %1', $names[$eventName])
        );
        $resultPage->getConfig()
                   ->getTitle()->prepend(__('Autoresponders'));
        $resultPage->getConfig()
                   ->getTitle()
                   ->prepend(
                       $model->getId() ? $model->getName() : __('New Autoresponder - %1', $names[$eventName]['name'])
                   );

        $resultPage->addContent(
            $resultPage->getLayout()
                       ->createBlock('Licentia\Panda\Block\Adminhtml\Autoresponders\Edit')
        )
                   ->addLeft(
                       $resultPage->getLayout()
                                  ->createBlock('Licentia\Panda\Block\Adminhtml\Autoresponders\Edit\Tabs')
                   );

        return $resultPage;
    }
}

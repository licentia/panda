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
use Magento\Framework\Exception\LocalizedException;

/**
 * Class Save
 *
 * @package Licentia\Panda\Controller\Adminhtml\Autoresponders
 */
class Save extends \Licentia\Panda\Controller\Adminhtml\Autoresponders
{

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig;

    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected \Magento\Catalog\Api\ProductRepositoryInterface $productRepository;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone;

    /**
     * @var \Licentia\Panda\Model\TagsFactory
     */
    protected \Licentia\Panda\Model\TagsFactory $tagsFactory;

    /**
     * @param \Licentia\Panda\Model\TagsFactory                    $tagsFactory
     * @param Action\Context                                       $context
     * @param \Magento\Catalog\Api\ProductRepositoryInterface      $productRepository
     * @param \Magento\Framework\View\Result\PageFactory           $resultPageFactory
     * @param \Magento\Framework\Registry                          $registry
     * @param \Licentia\Panda\Helper\Data                          $pandaHelper
     * @param \Licentia\Panda\Model\AutorespondersFactory          $autorespondersFactory
     * @param \Magento\Framework\App\Config\ScopeConfigInterface   $scopeConfigInterface
     * @param \Magento\Backend\Model\View\Result\ForwardFactory    $resultForwardFactory
     * @param \Magento\Framework\View\Result\LayoutFactory         $resultLayoutFactory
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone
     */
    public function __construct(
        \Licentia\Panda\Model\TagsFactory $tagsFactory,
        Action\Context $context,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Registry $registry,
        \Licentia\Panda\Helper\Data $pandaHelper,
        \Licentia\Panda\Model\AutorespondersFactory $autorespondersFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfigInterface,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory,
        \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone
    ) {

        parent::__construct(
            $context,
            $resultPageFactory,
            $registry,
            $pandaHelper,
            $autorespondersFactory,
            $resultForwardFactory,
            $resultLayoutFactory
        );

        $this->tagsFactory = $tagsFactory;
        $this->timezone = $timezone;
        $this->scopeConfig = $scopeConfigInterface;
        $this->productRepository = $productRepository;
    }

    /**
     * Save action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {

        parent::execute();
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        // check if data sent
        $data = $this->getRequest()->getPostValue();
        if ($data) {
            $id = $this->getRequest()->getParam('id');
            $event = isset($data['event']) ? $data['event'] : null;

            $model = $this->registry->registry('panda_autoresponder');

            if (!$event && $model->getEvent()) {
                $event = $model->getEvent();
            }

            if (!$model->getId() && $id) {
                $this->messageManager->addErrorMessage(__('This Autoresponder no longer exists.'));

                return $resultRedirect->setPath('*/*/');
            }

            try {
                if (isset($data['utm_parameter'])) {
                    $data['utm'] = json_encode(
                        [
                            'utm_parameter' => $data['utm_parameter'],
                            'utm_condition' => $data['utm_condition'],
                            'utm_match'     => $data['utm_match'],
                        ]
                    );

                    unset($data['utm_parameter'], $data['utm_condition'], $data['utm_match']);
                }

                if ($model->getId()) {
                    if (!isset($data['segments_ids'])) {
                        $data['segments_ids'] = [0];
                    }
                    if (array_search(0, $data['segments_ids']) !== false) {
                        $data['segments_ids'] = [];
                    }
                    $data['segments_ids'] = implode(',', $data['segments_ids']);

                    if (!isset($data['store_id'])) {
                        $data['store_id'] = [0];
                    }
                    if (array_search(0, $data['store_id']) !== false) {
                        $data['store_id'] = [];
                    }
                    $data['store_id'] = implode(',', $data['store_id']);

                    if (!isset($data['payment_method'])) {
                        $data['payment_method'] = [];
                    }
                    $data['payment_method'] = implode(',', $data['payment_method']);

                    if (!isset($data['shipping_method'])) {
                        $data['shipping_method'] = [];
                    }
                    $data['shipping_method'] = implode(',', $data['shipping_method']);
                }

                $data['product'] = isset($data['product']) ? trim($data['product']) : null;

                $model->setData('controller_panda', true);

                if ($event == 'order_product') {
                    $productsTemp1 = explode("\n", $data['product']);
                    $productsTemp = array_map("trim", $productsTemp1);

                    foreach ($productsTemp as $key => $product) {
                        if (strlen($productsTemp[$key]) == 0) {
                            unset($productsTemp[$key]);
                        }

                        try {
                            $okP = $this->productRepository->get($product);
                        } catch (\Exception $e) {
                            throw new LocalizedException(__('Cannot load product with SKU %1', $product));
                        }

                        if ($okP && $okP->getId()) {
                            $productsTemp[$key] = $product;
                            continue;
                        }
                        unset($productsTemp[$key]);
                    }
                    $data['product'] = implode(',', $productsTemp);
                }

                if ($event == 'customer_group') {
                    if (!isset($data['old_customer_group_id'])) {
                        $data['old_customer_group_id'] = [0];
                    }
                    if (array_search(0, $data['old_customer_group_id']) !== false) {
                        $data['old_customer_group_id'] = [];
                    }
                    $data['old_customer_group_id'] = implode(',', $data['old_customer_group_id']);

                    if (!isset($data['new_customer_group_id'])) {
                        $data['new_customer_group_id'] = [0];
                    }
                    if (array_search(0, $data['new_customer_group_id']) !== false) {
                        $data['new_customer_group_id'] = [];
                    }
                    $data['new_customer_group_id'] = implode(',', $data['new_customer_group_id']);
                }

                if (isset($data['shipping_method']) && is_array($data['shipping_method'])) {
                    $data['shipping_method'] = implode(',', $data['shipping_method']);
                }

                if (isset($data['payment_method']) && is_array($data['payment_method'])) {
                    $data['payment_method'] = implode(',', $data['payment_method']);
                }

                if ($event == 'product_cycle') {

                    if (!isset($data['categories']) && !isset($data['products'])) {
                        throw new LocalizedException(__('Please select one/multiple categories or add Products SKU(s)'));
                    }

                    if (isset($data['products'])) {

                        $productsTemp1 = explode("\n", $data['products']);
                        $productsTemp = array_map("trim", $productsTemp1);
                        $productsTemp = array_filter($productsTemp);

                        foreach ($productsTemp as $key => $product) {
                            if (strlen($productsTemp[$key]) == 0) {
                                unset($productsTemp[$key]);
                            }

                            try {
                                $okP = $this->productRepository->get($product);
                                if ($okP && $okP->getId()) {
                                    $productsTemp[$key] = $product;
                                    continue;
                                }
                            } catch (\Exception $e) {
                                unset($productsTemp[$key]);
                            }
                        }

                        if (!isset($data['categories']) && !$productsTemp) {
                            throw new LocalizedException(__('Please select one/multiple categories or add Products SKU(s)'));
                        }

                        $data['products'] = implode(',', $productsTemp);
                    }

                    if (isset($data['categories'])) {
                        if (!is_array($data['categories'])) {
                            $data['categories'] = [];
                        }
                        $data['categories'] = array_filter($data['categories']);
                        $data['categories'] = implode(',', $data['categories']);
                    }

                }

                $validateResult = $model->validateData(new \Magento\Framework\DataObject($data));
                if ($validateResult !== true) {
                    foreach ($validateResult as $errorMessage) {
                        $this->messageManager->addErrorMessage($errorMessage);
                    }
                    $this->_getSession()->setFormData($data);

                    return $resultRedirect->setPath(
                        '*/*/edit',
                        [
                            'id'     => $model->getId(),
                            'active_tab' => $this->getRequest()->getParam('active_tab'),
                        ]
                    );
                }

                if (isset($data['rule'])) {
                    $data['conditions'] = $data['rule']['conditions'];
                    unset($data['rule']);
                }
                $model->loadPost($data);
                $model->setData('controller_panda', true);

                $model->addData($data);
                $model->save();

                if (!isset($data['tags'])) {
                    $data['tags'] = [];
                }
                $this->tagsFactory->create()->updateTags('autoresponders', $model, $data['tags']);

                $this->messageManager->addSuccessMessage(__('You saved the Autoresponder.'));
                $this->_getSession()->setFormData(false);

                if ($this->getRequest()->getParam('back') || !$this->getRequest()->getParam('active')) {
                    return $resultRedirect->setPath(
                        '*/*/edit',
                        [
                            'id'     => $model->getId(),
                            'event'  => $event,
                            'active_tab' => $this->getRequest()->getParam('active_tab'),
                        ]
                    );
                }

                return $resultRedirect->setPath('*/*/');
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage(
                    $e,
                    __('Something went wrong while saving the Autoresponder. Check the error log for more information.')
                );
            }

            $this->_getSession()->setFormData($data);

            return $resultRedirect->setPath(
                '*/*/edit',
                [
                    'id'     => $model->getId(),
                    'event'  => $event,
                    'active_tab' => $this->getRequest()->getParam('active_tab'),
                ]
            );
        }

        return $resultRedirect->setPath('*/*/');
    }
}

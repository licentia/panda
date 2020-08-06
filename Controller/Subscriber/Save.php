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

namespace Licentia\Panda\Controller\Subscriber;

/**
 * Class Save
 *
 * @package Licentia\Panda\Controller\Subscriber
 */
class Save extends \Licentia\Panda\Controller\Subscriber
{

    /**
     * @return \Magento\Framework\App\ResponseInterface
     */
    public function execute()
    {

        if (!$this->formKeyValidator->validate($this->getRequest())) {
            return $this->_redirect('customer/account/');
        }

        $customerId = $this->customerSession->getCustomerId();
        if ($customerId === null) {
            $this->messageManager->addErrorMessage(__('Something went wrong while saving your subscription.'));
        } else {
            try {
                $customer = $this->customerRepository->getById($customerId);
                $storeId = $this->storeManager->getStore()->getId();
                $customer->setStoreId($storeId);
                #$this->customerRepository->save($customer);
                if ((boolean) $this->getRequest()->getParam('is_subscribed', false)) {
                    $this->coreSubscribersFactory->create()->subscribeCustomerById($customerId);
                    $this->messageManager->addSuccessMessage(__('We saved the subscription.'));
                } else {
                    $this->coreSubscribersFactory->create()->unsubscribeCustomerById($customerId);
                    $this->messageManager->addSuccessMessage(__('We removed the subscription.'));
                }

                /** @var \Licentia\Forms\Model\Forms $form */
                $form = $this->formsFactory->create()
                                           ->getFormForManagePage(
                                               $this->storeManager->getStore()->getId()
                                           );

                if ($form && $form->getId()) {
                    $this->formEntriesFactory->create()
                                             ->loadDataFromRequest()
                                             ->setFormId($form->getId())
                                             ->validateElements()
                                             ->save();
                }
            } catch (\Magento\Framework\Validator\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(
                    __('Something went wrong while saving your subscription.')
                );
            }
        }
        $this->_redirect('newsletter/manage/');
    }
}

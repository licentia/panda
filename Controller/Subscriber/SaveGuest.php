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

namespace Licentia\Panda\Controller\Subscriber;

/**
 * Class SaveGuest
 *
 * @package Licentia\Panda\Controller\Subscriber
 */
class SaveGuest extends \Licentia\Panda\Controller\Subscriber
{

    /**
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {

        if (!$this->formKeyValidator->validate($this->getRequest())) {
            return $this->resultRedirectFactory->create()->setUrl($this->_redirect->getRefererUrl());
        }

        $code = $this->getRequest()->getParam('code');

        /** @var \Licentia\Panda\Model\Subscribers $subscriber */
        $subscriber = $this->subscribersFactory->create()->loadByCode($code);

        if (!$subscriber->getId()) {
            $this->messageManager->addErrorMessage(__('Something went wrong while saving your subscription.'));
        } else {
            try {
                if ((boolean) $this->getRequest()->getParam('is_subscribed', false)) {
                    $subscriber->subscribe();
                    $this->messageManager->addSuccessMessage(__('We saved the subscription.'));
                } else {
                    $subscriber->unsubscribe();
                    $this->messageManager->addSuccessMessage(__('We removed the subscription.'));
                }

                $form = $this->formsFactory->create()
                                           ->getFormForManagePage(
                                               $this->storeManager->getStore()
                                                                  ->getId()
                                           );

                if ($form && $form->getId()) {
                    $this->formEntriesFactory->create()
                                             ->loadDataFromRequest()
                                             ->setData('subscriber', $subscriber)
                                             ->setData('form_id', $form->getId())
                                             ->validateElements()->save();
                }
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(__('Something went wrong while saving your subscription.'));
            }
        }

        $this->_redirect('/');
    }
}

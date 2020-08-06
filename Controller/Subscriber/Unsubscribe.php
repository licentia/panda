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
 * Class Unsubscribe
 *
 * @package Licentia\Panda\Controller\Subscriber
 */
class Unsubscribe extends \Licentia\Panda\Controller\Subscriber
{

    /**
     * @return \Magento\Framework\Controller\Result\Redirect
     * @throws \Exception
     */
    public function execute()
    {

        $resultRedirect = $this->resultRedirectFactory->create();

        $camp = $this->getRequest()->getParam('c');
        $id = $this->getRequest()->getParam('id');
        $code = $this->getRequest()->getParam('code');

        /** @var \Licentia\Panda\Model\Subscribers $subscriber */
        $subscriber = $this->subscribersFactory->create()->loadById($id);

        if ($subscriber->getCode() != $code || !$subscriber->getId()) {
            $this->messageManager->addErrorMessage(__('There was a problem with the un-subscription.'));

            return $resultRedirect->setPath('/');
        }
        /** @var \Licentia\Panda\Model\Campaigns $campaign */
        $campaign = $this->campaignsFactory->create()->load($camp);
        if ($campaign->getId()) {
            $this->unsubscribesFactory->create()->unsubscribe($campaign, $subscriber);
        }

        $coreN = $this->coreSubscriberCollection->create()
                                                ->addFieldToFilter('subscriber_email', $subscriber->getEmail())
                                                ->addFieldToFilter('store_id', $subscriber->getStoreId());

        try {
            if ($coreN->count() == 1) {
                $coreN->getFirstItem()->unsubscribe();
            } else {
                $subscriber->unsubscribe();
            }

            $this->messageManager->addSuccessMessage(__('You have been unsubscribed.'));
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage($e, __('There was a problem with the un-subscription.'));
        }

        return $resultRedirect->setPath($this->_redirect->getRefererUrl());
    }
}

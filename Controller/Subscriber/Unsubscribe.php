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
 * @modified   29/01/20, 15:22 GMT
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

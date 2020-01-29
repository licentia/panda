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

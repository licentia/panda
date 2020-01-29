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

namespace Licentia\Panda\Controller\Adminhtml\Account;

/**
 * Class Save
 *
 * @package Licentia\Panda\Controller\Adminhtml\Account
 */
class Save extends \Licentia\Panda\Controller\Adminhtml\Account
{

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
        $data = $this->getRequest()->getPostValue();

        if ($data) {
            try {
                if (isset($data['recover']) && $data['recover'] == 1) {
                    $this->api->recoverAPI(isset($data['email']) ? $data['email'] : '');

                    $this->messageManager->addSuccessMessage(
                        __(
                            'If there is any account in our database with the email you provided, you will receive the information within minutes. Make sure the email you provided has an active account. If you have any questions, please contact us.'
                        )
                    );
                }

                if (isset($data['cancelAccount'], $data['apikey']) && $data['cancelAccount'] == 1) {
                    $result = $this->api->validateApikey(['apikey' => $data['apikey']]);

                    if ($result) {
                        $this->api->cancelAccount();

                        $this->config->saveConfig('panda_general/info/apikey', '', 'default', 0);
                        $this->config->saveConfig('panda_general/info/validated', 0, 'default', 0);
                        $this->reinitableConfig->reinit();
                        $this->storeManager->reinitStores();
                    }

                    $this->messageManager->addSuccessMessage(
                        __(
                            'Account Canceled. All your remote data will be deleted within 24 hours. Data stored locally will remain'
                        )
                    );
                }

                if (isset($data['new'], $data['apikey']) && $data['new'] == 1) {
                    $result = $this->api->validateApikey(['apikey' => $data['apikey']]);

                    if ($result) {
                        $this->config->saveConfig('panda_general/info/apikey', $data['apikey'], 'default', 0);
                        $this->config->saveConfig('panda_general/info/validated', 1, 'default', 0);
                        $this->reinitableConfig->reinit();
                        $this->storeManager->reinitStores();
                    }

                    $this->messageManager->addSuccessMessage(__('Account API Information Updated'));
                }

                if (isset($data['updateApi'], $data['apikey']) && $data['updateApi'] == 1) {
                    $result = $this->api->validateApikey(['apikey' => $data['apikey']]);

                    if ($result) {
                        $this->config->saveConfig('panda_general/info/apikey', $data['apikey'], 'default', 0);
                        $this->config->saveConfig('panda_general/info/validated', 1, 'default', 0);
                        $this->reinitableConfig->reinit();
                        $this->storeManager->reinitStores();
                    }

                    $this->messageManager->addSuccessMessage(__('Account API Information Updated'));
                }

                if (isset($data['update']) && $data['update'] == 1) {
                    $countryCode = \Licentia\Panda\Helper\Data::getPrefixForCountry($data['country']);
                    $cellphone = preg_replace('/\D/', '', $data['cellphone']);
                    $cellphone = $countryCode . '-' . ltrim($cellphone, $countryCode);

                    $this->api->validateCellphone($cellphone);
                    $this->api->updateAccount($data);
                    $this->messageManager->addSuccessMessage(__('Account Information Updated'));
                }

                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            }
        }

        return $resultRedirect->setPath('*/*/');
    }
}

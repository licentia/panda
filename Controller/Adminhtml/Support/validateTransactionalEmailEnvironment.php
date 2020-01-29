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

namespace Licentia\Panda\Controller\Adminhtml\Support;

/**
 * Class validateTransactionalEmailEnvironment
 *
 * @package Licentia\Panda\Controller\Adminhtml\Support
 */
class validateTransactionalEmailEnvironment extends \Licentia\Panda\Controller\Adminhtml\Support
{

    /**
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {

        $email = $this->scopeConfig->getValue('panda_nuntius/transactional/email',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE);

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->messageManager->addErrorMessage(__('Please insert a valid Email Address'));

            return $this->resultRedirectFactory->create()->setUrl($this->_redirect->getRefererUrl());
        }

        try {
            $smtp = $this->scopeConfig->getValue(
                'panda_nuntius/transactional',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
            $fromName = $this->scopeConfig->getValue(
                'trans_email/ident_sales/name',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
            $fromEmail = $this->scopeConfig->getValue(
                'trans_email/ident_sales/email',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
            $siteName = $this->scopeConfig->getValue(
                'general/store_information/name',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );

            if (!isset($smtp['server']) || !isset($smtp['auth']) || !isset($smtp['username'])) {
                throw new \Exception('Please fill the Server Details in the frame above');
            }

            $config = ['auth' => $smtp['auth'], 'port' => $smtp['port']];

            if ($smtp['ssl'] != 'none') {
                $config['ssl'] = $smtp['ssl'];
            }
            if ($smtp['auth'] != 'none') {
                $config['username'] = $smtp['username'];
                $config['password'] = $this->encryptor->decrypt($smtp['password']);
            } else {
                unset($config['auth']);
            }

            $transport = new \Zend_Mail_Transport_Smtp($smtp['server'], $config);

            $mail = new \Zend_Mail('UTF-8');
            $mail->setFrom($fromEmail, $fromName);
            $mail->setSubject('Test Transactional Settings for: ' . $siteName);

            $message = "Hi there,<br><br>This is a test message from $siteName to verify Transactional Mail Settings.<br><br>If you can read this its because everything is working as expected<br>Regards";

            $mail->setBodyHtml($message);
            $mail->addTo($email);
            $mail->send($transport);

            $this->messageManager->addSuccessMessage(
                'Everything seems to be OK with your Transactional Email Settings'
            );
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__('Error Testing Your Settings: ') . $e->getMessage());
        }

        return $this->resultRedirectFactory->create()->setUrl($this->_redirect->getRefererUrl());
    }
}

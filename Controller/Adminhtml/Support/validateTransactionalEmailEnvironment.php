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
